<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    /**
     * Dipanggil widget secara polling (setiap beberapa detik).
     * ?after_id= dipakai supaya tidak narik ulang semua pesan setiap kali —
     * cukup pesan yang lebih baru dari id terakhir yang sudah diterima client.
     */
    public function messages(Request $request)
    {
        $userId = auth()->id();
        $afterId = (int) $request->query('after_id', 0);

        $query = ChatMessage::where('user_id', $userId)->visibleToUser()->orderBy('id');

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        } else {
            // Load awal: batasi 50 pesan terakhir saja, jangan seluruh histori.
            $query->orderByDesc('id')->limit(50);
        }

        $messages = $query->get();
        if ($afterId === 0) {
            $messages = $messages->sortBy('id')->values();
        }

        // Tandai "sudah dibaca" hanya kalau ini benar-benar buka popup (bukan
        // polling badge di background saat panel masih tertutup — lihat ?peek=1).
        if (!$request->boolean('peek')) {
            ChatMessage::where('user_id', $userId)
                ->where('sender', 'admin')
                ->unread()
                ->update(['is_read' => true]);
        }

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

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => trim($request->message),
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender' => 'user',
                'message' => $message->message,
                'time' => $message->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * "Hapus" di sisi user hanya menyembunyikan pesan dari TAMPILAN user.
     * Admin tetap bisa melihat seluruh riwayat percakapan ini di panel admin —
     * data pesan tidak benar-benar dihapus dari database.
     */
    public function destroy()
    {
        ChatMessage::where('user_id', auth()->id())
            ->visibleToUser()
            ->update(['deleted_by_user_at' => now()]);

        return response()->json(['message' => 'Riwayat chat di sisi kamu sudah dihapus.']);
    }
}
