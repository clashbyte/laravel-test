<?php


namespace App\Http\Controllers;


use App\Ticket;
use Auth;
use Illuminate\Http\Request;

class ClientController extends Controller
{

    /**
     * Выдача страницы добавления тикета
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function ticketForm() {
        if (!Ticket::canCreateNew(Auth::user())) {
            \Session::flash('error', 'Вы не можете создать больше одного обращения в сутки');
            return redirect('home');
        }
        return view('tickets.new');
    }

    /**
     * Запись тикета в базу
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveTicket(Request $request) {
        if (!$request->filled(['subject', 'content'])) {
            \Session::flash('error', 'Форма недозаполнена');
            return redirect('home');
        }

        $ticket = Ticket::createBasic(Auth::user(), $request->input('subject'), $request->input('content'));
        if ($request->hasFile('attachment')) {
            $ticket->attachFile($request->file('attachment'));
        }

        \Session::flash('status', 'Обращение №'.$ticket->id.' зарегистрировано');
        return redirect('home');
    }



}
