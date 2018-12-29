<?php

namespace app\index\Model;


use think\Model;

class Itinerary extends Model
{
    protected $insert = ['status' => 1];
    protected $type = [
        'start_time' => 'datetime:Y-m-d',
        'end_time' => 'datetime:Y-m-d',
    ];

    public function createItinerary($data) {
        $this->save($data);
    }
}