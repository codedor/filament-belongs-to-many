<?php

namespace Codedor\BelongsToMany\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    public function tags()
    {
        return $this->belongsToMany(TestTag::class);
    }
}
