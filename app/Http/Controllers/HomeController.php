<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = \Auth::user()->role;
        $tickets = [];
        $users = [];
        if ($role == User::ROLE_MANAGER) {
            // Пользователь - менеджер
            $tickets = Ticket::orderByDesc('id')->get();
        } else {
            // Пользователь - обычный
            $tickets = Ticket::where('user', \Auth::id())->orderByDesc('id')->get();
        }
        $users = User::whereIn('id', $tickets->pluck('user')->merge($tickets->pluck('manager'))->unique())->get(['id', 'email', 'name'])->keyBy('id');


        /**
         * Выдача шаблона
         */
        return view('tickets.list', [
            'tickets' => $tickets,
            'users' => $users,
            'is_manager' => $role == User::ROLE_MANAGER,
        ]);
    }

    /**
     * Выдача формы создания заявки
     */
    public function newTicket() {
        // Проверка пользователя
        if (!$this->checkTicketAccess()) return redirect('/home');
        return view('tickets.new');
    }

    /**
     * Форма ответа на обращение
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function newReply($id) {

        // Поиск тикета
        $t = $this->pickTicketForReply($id);
        if (!$t) return redirect('/home');

        // Выдача ответа
        return view('tickets.reply', [
            'ticket' => $t,
            'user' => User::find($t->user, ['id', 'name', 'email'])
        ]);
    }

    /**
     * Запись тикета в базу
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveTicket(Request $request) {

        // Проверка пользователя
        if (!$this->checkTicketAccess()) return redirect('/home');

        // Сохранение тикета
        if (!$request->filled(['subject', 'content'])) {
            \Session::flash('status', 'Форма недозаполнена');
            return redirect('/home');
        }
        $t = new Ticket();
        $t->user = \Auth::id();
        $t->subject = $request->input('subject', '');
        $t->content = $request->input('content', '');
        $t->save();

        // Дополняем файл
        if ($request->hasFile('attachment')) {
            $t->attachment = $request->file('attachment')->store('attachments');
            $t->save();
        }

        // Переброс на список
        \Session::flash('status', 'Обращение №'.$t->id.' зарегистрировано');
        return redirect('/home');
    }

    /**
     * Сохранение ответа
     */
    public function saveReply(Request $request, $id) {

        // Поиск тикета
        $t = $this->pickTicketForReply($id);
        if (!$t) return redirect('/home');

        // Запись ответа
        if (!$request->filled('reply')) {
            \Session::flash('status', 'Форма недозаполнена');
            return redirect('/home');
        }
        $t->manager = \Auth::id();
        $t->reply = $request->input('reply', '');
        $t->replied_at = Carbon::now();
        $t->save();

        // Редирект
        \Session::flash('status', 'Ответ сохранён');
        return redirect('/home');
    }

    /**
     * Получение вложения
     * @param $id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|void
     */
    public function getFile($id) {
        // Поиск тикета
        $t = Ticket::find($id, ['user', 'attachment']);
        if (!$t || !$t->attachment || (\Auth::user()->role == User::ROLE_USER && $t->user != \Auth::id())) {
            return abort(404);
        }
        return \Storage::download($t->attachment);
    }

    /**
     * Валидация доступа к созданию обращений
     * @return bool
     */
    private function checkTicketAccess() {
        // Проверка на роль
        if (\Auth::user()->role != User::ROLE_USER) {
            \Session::flash('error', 'Вы не можете создать новое обращение');
            return false;
        }

        // Проверка на последний пост
        $prev_time = Carbon::make(Ticket::where('user', \Auth::id())->max('created_at'));
        if ($prev_time) {
            if (Carbon::now()->timestamp - $prev_time->timestamp < 86400) {
                \Session::flash('error', 'Вы не можете создать более одного обращения в сутки');
                return false;
            }
        }

        return true;
    }

    /**
     * Валидация доступа к созданию обращений
     * @return bool
     */
    private function pickTicketForReply($id) {
        // Проверка на роль
        if (\Auth::user()->role != User::ROLE_MANAGER) {
            \Session::flash('error', 'Вы не можете отвечать на обращения');
            return null;
        }

        // Поиск и выдача
        $t = Ticket::find($id);
        if (!$t || $t->manager != null) {
            \Session::flash('error', 'Обращение недоступно');
            return null;
        }
        return $t;
    }
}
