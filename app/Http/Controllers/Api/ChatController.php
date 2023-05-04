<?php

namespace App\Http\Controllers\Api;

use App\Events\SendMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ChatListResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends BaseController
{

    /**
     * @OA\Get(
     *    path="/chats",
     *    operationId="getChats",
     *    tags={"Get Chats"},
     *    summary="Get Chats",
     *    description="Get Chats",
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */

    public function getChats(Request $request)
    {
        $chats = Chat::where('owner_id', Auth::id())
            ->orWhere('user_id', Auth::id())
            ->paginate(10);
        
        return ChatListResource::collection($chats);
    }


    /**
     * @OA\Get(
     *    path="/chat/{chat}",
     *    operationId="getChat",
     *    tags={"Get Chat"},
     *    summary="Get Chat",
     *    description="Get Chat",
     *    @OA\Parameter(name="id", in="query", description="ID", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="owner_id", in="query", description="owner_id", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="user_id", in="query", description="user_id", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="owner_message_count", in="query", description="owner_message_count", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *    @OA\Parameter(name="user_message_count", in="query", description="user_message_count", required=false,
     *        @OA\Schema(type="string")
     *    ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *          @OA\Property(property="status_code", type="integer", example="200"),
     *          @OA\Property(property="data",type="object")
     *           ),
     *        )
     *       )
     *  )
     */

    public function getChat(Chat $chat)
    {
        if ($chat->checkUser(Auth::user()) === false) {
            return $this->sendError('Чат не найден');
        }
        $count_name = $chat->getMessageCountUserName(Auth::user());
    
        if ($count_name === 'owner'){
            $chat->update([
                'owner_message_count' => 0
            ]);
        }
        if ($count_name === 'user'){
            $chat->update([
                'user_message_count' => 0
            ]);
        }
        
        $chat->messages()
            ->where('user_id', '!=', Auth::id())
            ->where('is_read', 0)
            ->update([
                'is_read' => true,
            ]);
        return MessageResource::collection($chat->messages()->where('deleted_id', '!=', Auth::id())->orderBy('id', 'desc')
            ->paginate(20));
    }


    /**
     * @OA\Post(
     *     path="/chat/send-message",
     *     operationId="sendMessage",
     *     tags={"Send Message"},
     *     summary="Send Message",
     *     description="Send Message",
     *     @OA\Parameter(name="id", in="path", description="Id of User", required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *           required={"message", "user_id"},
     *            @OA\Property(property="message", type="integer", format="integer", example="message"),
     *            @OA\Property(property="user_id", type="string", format="string", example="user id")
     *        ),
     *     ),
     *     @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(
     *             @OA\Property(property="status_code", type="integer", example="200"),
     *             @OA\Property(property="data",type="object")
     *          )
     *       )
     *  )
     */

    public function sendMessage(SendMessageRequest $request)
    {
        $chat = Chat::find($request->get('chat_id'));
        if (!$chat || $chat->checkUser(Auth::user()) === false) {
            return $this->sendError('Чат не найден');
        }
        $user_id = Auth::id();
        
        $message = $chat->messages()
            ->create([
                'message' => htmlspecialchars($request->get('message')),
                'user_id' => $user_id,
            ]);
        if ($message) {
            if ($chat->owner_id == $user_id) {
                $chat->user_message_count++;
                $chat->save();
            } else {
                $chat->owner_message_count++;
                $chat->save();
            }
        }
        event(new SendMessageEvent($chat, $message));
        return $this->sendResponse(MessageResource::make($message), 'Сообщение отправлено');
    }

    /**
     * @OA\Delete(
     *    path="/message/delete/{message}",
     *    operationId="deleteMessage",
     *    tags={"Delete Message"},
     *    summary="Delete Message",
     *    description="Delete Message",
     *    @OA\Parameter(name="id", in="path", description="Id of Message", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */

    public function deleteMessage(Request $request, Message $message)
    {
        $chat = $message->chat;
        if ($chat->checkUser(Auth::user()) === false){
            return $this->sendError('Сообщение не найдено');
        }
        
        if ($message->deleted_id && $message->deleted_id === Auth::id()){
            return $this->sendError('Сообщение уже удалено');
        }
        if ($message->deleted_id && $message->deleted_id !== Auth::id()){
            $message->delete();
            if ($chat->getMessageCountUserName(Auth::user()) == 'owner'){
                $chat->user_message_count < 0 ? $chat->user_message_count-- : 0;
                $chat->save();
            }
            if ($chat->getMessageCountUserName() == 'user'){
                $chat->owner_message_count < 0 ? $chat->owner_message_count-- : 0;
                $chat->save();
            }
            return $this->sendResponse('Сообщение удалено');
        }
        $message->update([
            'deleted_id' => Auth::id()
        ]);
        return $this->sendResponse('Сообщение удалено');
    }

    /**
     * @OA\Delete(
     *    path="/chat/delete/{chat}",
     *    operationId="deleteChat",
     *    tags={"Delete Chat Message"},
     *    summary="Delete Chat Message",
     *    description="Delete Chat Message",
     *    @OA\Parameter(name="id", in="path", description="Id of Chat", required=true,
     *        @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *         @OA\Property(property="status_code", type="integer", example="200"),
     *         @OA\Property(property="data",type="object")
     *          ),
     *       )
     *      )
     *  )
     */

    public function deleteChat(Request $request, Chat $chat)
    {
        if ($chat->checkUser(Auth::user()) === false){
            return $this->sendError('Чат не найден');
        }
        if (!$chat->messages){
            return $this->sendError('Сообщения уже удалены');
        }
        $chat->messages()->where('user_id', Auth::id())->update([
            'deleted_id' => Auth::id()
        ]);
        return $this->sendResponse('Чат удален');
    }
}
