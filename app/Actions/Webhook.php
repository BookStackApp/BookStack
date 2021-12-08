<?php

namespace BookStack\Actions;

use BookStack\Interfaces\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $endpoint
 */
class Webhook extends Model implements Loggable
{
    use HasFactory;

    /**
     * Get the string descriptor for this item.
     */
    public function logDescriptor(): string
    {
        return "({$this->id}) {$this->name}";
    }
}
