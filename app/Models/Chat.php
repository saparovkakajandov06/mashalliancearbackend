<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Chat
 *
 * @property int $id
 * @property int $owner_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUserId($value)
 * @property int|null $owner_message_count
 * @property int|null $user_message_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $owner
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereOwnerMessageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUserMessageCount($value)
 * @mixin \Eloquent
 */
class Chat extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }
    
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }
    
    public function checkUser(User $user): bool
    {
        if ($this->owner_id == $user->id || $this->user_id == $user->id){
            return true;
        }
        return false;
    }
    
    public function getMessageCountUserName(User $user)
    {
        if ($this->owner_id == $user->id){
            return 'owner';
        }
        if ($this->user_id == $user->id){
            return 'user';
        }
    }
}
