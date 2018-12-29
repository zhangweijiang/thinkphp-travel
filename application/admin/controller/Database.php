<?php


namespace app\admin\controller;

use think\Db;

class Database extends BaseController
{

    /**
     * 数据库备份/还原列表
     * @param String $type import-还原，export-还原
     * @return mixed
     */
    public function index($type = NULL)
    {
        switch ($type) {
            /* 数据还原 */
            case 'import':
                //列出备份文件列表
                $path = config('data_backup_path');
                if (!is_dir($path)) {//检查该文件是否为目录
                    mkdir($path, 0755, true);//创建目录
                }
                $path = realpath($path);//返回文件的绝对路径
                $flag = \FilesystemIterator::KEY_AS_FILENAME;
                $glob = new \FilesystemIterator($path, $flag);

                $list = array();
                foreach ($glob as $name => $file) {
                    if (preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql(?:\.gz)?$/', $name)) { //正则匹配，成功返回1，否则为0
                        $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d'); //根据指定的格式解析来自字符串的输入

                        $date = "{$name[0]}-{$name[1]}-{$name[2]}";//日期
                        $time = "{$name[3]}:{$name[4]}:{$name[5]}";//时间
                        $part = $name[6];

                        if (isset($list["{$date} {$time}"])) {//判断是否存在匹配该日期时间的文件
                            $info = $list["{$date} {$time}"];
                            $info['part'] = max($info['part'], $part);
                            $info['size'] = $info['size'] + $file->getSize();//将所有卷的备份文件大小相加，获得这次备份总的文件大小
                        } else {
                            $info['part'] = $part;
                            $info['size'] = $file->getSize();
                        }
                        $extension = strtoupper(pathinfo($file->getFilename(), PATHINFO_EXTENSION));//获取$file文件的后缀信息并将其转换为大写
                        $info['compress'] = ($extension === 'SQL') ? '-' : $extension;//获取文件压缩方式
                        $info['time'] = strtotime("{$date} {$time}");

                        $list["{$date} {$time}"] = $info;
                    }
                }
                $title = '数据还原';
                break;
            /* 数据备份 */
            case 'export':
                $Db = \think\Db::connect();
                $list = $Db->query('SHOW TABLE STATUS');//获取数据库中所有表信息
                $list = array_map('array_change_key_case', $list);//将$list中数组的下标转换为小写
                $title = '数据备份';
                break;
            default:
                return $this->error('参数错误！');
        }
        //渲染模板
        $this->assign('meta_title', $title);
        $this->assign('list', $list);

        return $this->fetch($type);
    }

    /**
     * 优化数据表
     * @param string|array $tables 表名
     */
    public function optimize($tables = NULL)
    {
        if ($tables) {
            $Db = Db::connect();
            if (is_array($tables)) { //判断是否为数组
                $tables = implode('`,`', $tables);
            }
            $list = $Db->query("OPTIMIZE TABLE `{$tables}`"); //执行sql语句optimize优化数据表$tables
            if ($list) {
                $this->success('数据表优化完成');
            } else {
                $this->error('数据表优化出错，请重试');
            }
        } else {
            $this->error('请指定需要优化的数据表');
        }
    }

    /**
     * 修复数据表
     * @param String|array $tables 表名
     */
    public function repair($tables = NULL)
    {
        if ($tables) { //判断是否存在数据库表名
            $Db = Db::connect();
            if (is_array($tables)) { //判断是否为数组
                $tables = implode('`,`', $tables);
            }
            $list = $Db->query("REPAIR TABLE `{$tables}`"); //执行sql语句repair修复数据表$tables
            if ($list) {
                $this->success('数据表修复完成');
            } else {
                $this->error('数据表修复出错，请重试');
            }
        } else {
            $this->error('请指定需要修复的数据表');
        }
    }

    /**
     * 删除数据库备份文件
     * @param int $time 备份时间
     */
    public function delete($time = 0)
    {
        if ($time) {
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR . $name;
            array_map("unlink", glob($path)); //将匹配$path的文件用unlink()函数进行删除
            if (count(glob($path))) { //统计匹配$path的文件数量,大于0则删除失败
                $this->error('备份文件删除失败，请检查权限');
            } else {
                $this->success('备份文件删除成功');
            }
        } else {
            $this->error('参数错误');
        }
    }

    /**
     * 备份数据库表
     * @param String $tables 需要备份的数据表
     * @param Integer $id 表id
     * @param Integer $start 起始行数
     */
    public function export($tables = NULL, $id = NULL, $start = NULL)
    {
        if (request()->isPost() && !empty($tables) && is_array($tables)) {
            //初始化
            $path = config('data_backup_path');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
            //读取备份配置
            $config = array('path' => realpath($path) . DIRECTORY_SEPARATOR, 'part' => config('data_backup_part_size'), 'compress' => config('data_backup_compress'), 'level' => config('data_backup_compress_level'));
            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                $this->error('检测到有一个备份任务正在执行，请稍后再试！');
            } else {
                //创建锁文件
                file_put_contents($lock, time());
            }
            //检查备份目录是否可写
            if (!is_writeable($config['path'])) {
                $this->error('备份目录不存在或不可写，请检查后重试！');
            }
            session('backup_config', $config);
            //生成备份文件信息
            $file = array('name' => date('Ymd-His', time()), 'part' => 1);
            session('backup_file', $file);
            //缓存要备份的表
            session('backup_tables', $tables);
            //创建备份文件
            $Database = new \app\common\common\Database($file, $config);
            if (false !== $Database->create()) {
                $tab = array('id' => 0, 'start' => 0);

                $this->success('初始化成功！', '', array('tables' => $tables, 'tab' => $tab));
            } else {
                $this->error('初始化失败，备份文件创建失败！');
            }
        } elseif (request()->isGet() && is_numeric($id) && is_numeric($start)) {
            //备份数据
            $tables = session('backup_tables');
            //备份指定表
            $Database = new \app\common\common\Database(session('backup_file'), session('backup_config'));
            $start = $Database->backup($tables[$id], $start);
            if (false === $start) {
                //出错
                $this->error('备份出错！');
            } elseif (0 === $start) {
                //下一表
                if (isset($tables[++$id])) {
                    $tab = array('id' => $id, 'start' => 0);

                    $this->success('备份完成！', '', array('tab' => $tab));
                } else {
                    //备份完成，清空缓存
                    unlink(session('backup_config.path') . 'backup.lock');
                    session('backup_tables', NULL);
                    session('backup_file', NULL);
                    session('backup_config', NULL);

                    $this->success('备份完成！');
                }
            } else {
                $tab = array('id' => $id, 'start' => $start[0]);
                $rate = floor(100 * ($start[0] / $start[1]));

                $this->success("正在备份...({$rate}%)", '', array('tab' => $tab));
            }
        } else {
            //出错
            $this->error('参数错误！');
        }
    }

    /**
     * 还原数据库表
     * @param int $time 备份时间
     * @param Integer $part 卷数
     * @param Integer $start 起始行数
     */
    public function import($time = 0, $part = null, $start = null) {
        if (is_numeric($time) && is_null($part) && is_null($start)) {
            //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path  = realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR . $name;
            $files = glob($path);
            $list  = array();
            foreach ($files as $name) {
                $basename        = basename($name);
                $match           = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz              = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);
            //检测文件正确性
            $last = end($list);
            if (count($list) === $last[0]) {
                session('backup_list', $list); //缓存备份列表
                $this->success('初始化完成！', '', array('part' => 1, 'start' => 0));
            } else {
                $this->error('备份文件可能已经损坏，请检查！');
            }
        } elseif (is_numeric($part) && is_numeric($start)) {
            $list = session('backup_list');

            $db = new \app\common\common\Database($list[$part], array('path' => realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR, 'compress' => $list[$part][2]));

            $start = $db->import($start);

            if (false === $start) {
                $this->error('还原数据出错！');
            } elseif (0 === $start) {
                //下一卷
                if (isset($list[++$part])) {
                    $data = array('part' => $part, 'start' => 0);
                    $this->success("正在还原...#{$part}", '', $data);
                } else {
                    session('backup_list', null);
                    $this->success('还原完成！');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if ($start[1]) {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $this->success("正在还原...#{$part} ({$rate}%)", '', $data);
                } else {
                    $data['gz'] = 1;
                    $this->success("正在还原...#{$part}", '', $data);
                }
            }
        } else {
            $this->error('参数错误！');
        }
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     */
    public function del($time = 0) {
        if ($time) {
            $name = date('Ymd-His', $time) . '-*.sql*';
            $path = realpath(config('data_backup_path')) . DIRECTORY_SEPARATOR . $name;
            array_map("unlink", glob($path));
            if (count(glob($path))) {
                $this->error('备份文件删除失败，请检查权限！');
            } else {
                $this->success('备份文件删除成功！');
            }
        } else {
            $this->error('参数错误！');
        }
    }

}