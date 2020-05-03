<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class Ticket extends Model
{
    protected $table = 'tickets';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Прикрепление файла к тикету
     * @param UploadedFile $file Загруженный файл
     * @return Ticket
     */
    public function attachFile(UploadedFile $file) {
        if (!$file || !$file->isValid()) {
            throw new \InvalidArgumentException('Файл не существует или загружен с ошибками');
        }
        $this->attachment = $file->store('attachments');
        $this->save();
        return $this;
    }

    /**
     * Добавление ответа к тикету
     * @param User $user
     * @param string $reply
     * @return Ticket
     */
    public function addReply(User $user, string $reply) {
        if (!$user || !$user->isManager()) {
            throw new \InvalidArgumentException('Пользователь равен null или не является менеджером');
        }
        $this->manager = $user->id;
        $this->reply = $reply;
        $this->replied_at = Carbon::now();
        $this->save();
        return $this;
    }

    /**
     * Создание тикета с базовыми полями
     * @param User $user Пользователь-владелец
     * @param string $subject Тема
     * @param string $content Содержимое
     * @return Ticket
     */
    public static function createBasic(User $user, string $subject, string $content) : Ticket {
        $ticket = new Ticket();
        $ticket->subject = $subject;
        $ticket->content = $content;
        $ticket->user = $user->id;
        $ticket->save();
        return $ticket;
    }

    /**
     * Поиск тикета для ответа
     * @param User $user Пользователь, который собирается отвечать
     * @param int $id Индекс тикета
     * @param array $fields Необходимые поля тикета
     * @return Ticket
     */
    public static function findForReply(User $user, int $id, array $fields = ['*']) : Ticket {
        if (!$user || !$user->isManager()) {
            throw new \InvalidArgumentException('Пользователь равен null или не является менеджером');
        }
        $ticket = Ticket::findOrFail($id, $fields);
        if (!$ticket->canUserReply($user)) {
            abort(403);
        }
        return $ticket;
    }

    /**
     * Получение списка тикетов для пользователя
     * @param User $user Пользователь
     * @return Collection
     */
    public static function forUser(User $user) : Collection {
        if (!$user) {
            throw new \InvalidArgumentException('Передан некорректный объект пользователя');
        }

        $tickets = collect();
        switch ($user->role) {

            case User::ROLE_USER:
                $tickets = Ticket::where('user', $user->id)->orderByDesc('id')->get();
                break;

            case User::ROLE_MANAGER:
                $tickets = Ticket::orderByDesc('id')->get();
                break;

        }
        return $tickets;
    }

    /**
     * Получение всех пользователей, использованных в списке
     * тикетов. Коллекция индексирована по ID пользователя.
     * @param Collection $tickets Тикеты
     * @param array|null $fields Поля пользователей
     * @return Collection
     */
    public static function listedUsers(Collection $tickets, array $fields = null) : Collection {
        $ids = $tickets->pluck('user')->merge($tickets->pluck('manager'))->filter(function ($value) {
            return !!$value;
        });
        if ($ids->count()) {
            if (is_array($fields) && !in_array('id', $fields)) {
                $fields[] = 'id';
            }
            return User::whereIn('id', $ids->unique())->get($fields)->keyBy('id');
        }
        return collect();
    }

    /**
     * Может ли пользователь создать новый тикет
     * @param User $user Пользователь
     * @return bool
     */
    public static function canCreateNew(User $user) : bool {

        // Проверка именно на пользователя
        if (!$user || !$user->isUser()) {
            return false;
        }

        // Проверка на последний пост
        $prev_time = Carbon::make(Ticket::where('user', $user->id)->max('created_at'));
        if ($prev_time) {
            if (Carbon::now()->timestamp - $prev_time->timestamp < 86400) {
                return false;
            }
        }
        return true;
    }

    /**
     * Может ли пользователь ответить на этот тикет
     * @param User $user Пользователь
     * @return bool
     */
    public function canUserReply(User $user) : bool {
        if (!$user || !$user->isManager()) {
            return false;
        }
        return $this->manager === null;
    }

    /**
     * Получение ссылки на файл. Если файла не существует - вернёт null
     * @param int $ticketID
     * @return string
     */
    public static function getAttachmentPath(int $ticketID) : string {
        $ticket = self::find($ticketID, ['user', 'attachment']);
        if (!$ticket || !$ticket->attachment || (Auth::user()->isUser() && $ticket->user != \Auth::id())) {
            return null;
        }
        return $ticket->attachment;
    }


}
