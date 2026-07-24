<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;

class AdminChatController extends Controller
{
    /**
     * Inbox: daftar user yang pernah chat, diurutkan dari pesan terbaru,
     * lengkap dengan badge jumlah pesan yang belum dibaca admin.
     */
    public function index()
    {
        $threads = User::query()->select('users.*')->join('chat_messages', 'chat_messages.user_id', '=', 'users.id')->whereNull('chat_messages.deleted_by_admin_at')->selectRaw('MAX(chat_messages.created_at) as last_message_at')->selectRaw("SUM(CASE WHEN chat_messages.sender = 'user' AND chat_messages.is_read = 0 THEN 1 ELSE 0 END) as unread_count")->groupBy('users.id')->orderByDesc('last_message_at')->paginate(20);

        // Pesan terakhir per thread (untuk preview di list) — 1 query kecil terpisah,
        // lebih murah daripada N+1 per user.
        $lastMessages = ChatMessage::whereIn('user_id', $threads->pluck('id'))
            ->visibleToAdmin()
            ->whereIn('id', function ($q) use ($threads) {
                $q->selectRaw('MAX(id)')->from('chat_messages')->whereIn('user_id', $threads->pluck('id'))->whereNull('deleted_by_admin_at')->groupBy('user_id');
            })
            ->get()
            ->keyBy('user_id');

        return view('admin.chat.index', compact('threads', 'lastMessages'));
    }

    public function show(User $user)
    {
        // Sengaja TIDAK pakai visibleToUser() — admin tetap harus bisa lihat
        // pesan yang sudah dihapus dari sisi user, untuk keperluan riwayat/CS.
        $messages = ChatMessage::where('user_id', $user->id)->visibleToAdmin()->orderBy('id')->limit(100)->get();

        ChatMessage::where('user_id', $user->id)
            ->where('sender', 'user')
            ->unread()
            ->update(['is_read' => true]);

        return view('admin.chat.show', compact('user', 'messages'));
    }

    public function messages(Request $request, User $user)
    {
        $afterId = (int) $request->query('after_id', 0);

        $query = ChatMessage::where('user_id', $user->id)->visibleToAdmin()->orderBy('id');
        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get();

        ChatMessage::where('user_id', $user->id)
            ->where('sender', 'user')
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(
                fn($m) => [
                    'id' => $m->id,
                    'sender' => $m->sender,
                    'message' => $m->message,
                    'time' => $m->created_at->format('H:i'),
                ],
            ),
        ]);
    }

    public function send(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'user_id' => $user->id,
            'sender' => 'admin',
            'message' => trim($request->message),
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender' => 'admin',
                'message' => $message->message,
                'time' => $message->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Dipanggil polling ringan di sidebar/navbar admin untuk badge notifikasi
     * "ada chat baru" tanpa harus buka halaman inbox.
     */
    public function unreadCount()
    {
        $count = ChatMessage::where('sender', 'user')->unread()->visibleToAdmin()->count();

        return response()->json(['unread_count' => $count]);
    }

    /**
     * "Hapus" di sisi admin hanya menyembunyikan pesan dari TAMPILAN admin.
     * Kalau user masih bisa lihat & lanjut chat, thread otomatis muncul lagi
     * di inbox begitu ada pesan baru (row baru punya deleted_by_admin_at = null).
     */
    public function destroy(User $user)
    {
        ChatMessage::where('user_id', $user->id)
            ->visibleToAdmin()
            ->update(['deleted_by_admin_at' => now()]);

        return redirect()
            ->route('admin.chat.index')
            ->with('success', 'Riwayat chat dengan ' . $user->name . ' berhasil dihapus dari panel admin.');
    }
}
