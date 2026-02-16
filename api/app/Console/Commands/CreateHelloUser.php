<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateHelloUser extends Command
{
    protected $signature = 'user:create-hello';
    protected $description = 'hello.sagaworld816@gmail.com / ryoya0107 でユーザーを1件作成または更新';

    public function handle(): int
    {
        $email = 'hello.sagaworld816@gmail.com';
        $password = 'ryoya0107';

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->is_active = true;
            $user->is_admin = true; // 管理画面 /admin にログインできるようにする
            $user->save();
            $this->info("既存ユーザーを更新しました: {$email}（管理者権限あり）");
        } else {
            User::create([
                'name' => 'User',
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_admin' => true, // 管理画面にログインできるようにする
            ]);
            $this->info("新規ユーザーを作成しました: {$email}（管理者権限あり）");
        }

        $this->info('ログイン: メール ' . $email . ' / パスワード ' . $password);
        return self::SUCCESS;
    }
}
