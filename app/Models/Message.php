<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $chat_id
 * @property int $user_id
 * @property string $message
 * @property int $is_read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereUserId($value)
 * @property int|null $deleted_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $deleted_user
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Message onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Message withoutTrashed()
 * @mixin \Eloquent
 */
class Message extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['_token'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
    
    public function deleted_user()
    {
       return $this->belongsTo(User::class, 'deleted_id', 'id');
    }
}
