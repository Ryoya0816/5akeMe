<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--email= : 管理者のメールアドレス}
                            {--name= : 管理者の名前}
                            {--password= : パスワード}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '管理者ユーザーを作成します';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('メールアドレスを入力してください');
        $name = $this->option('name') ?? $this->ask('名前を入力してください');
        $password = $this->option('password') ?? $this->secret('パスワードを入力してください');

        // バリデーション
        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ], [
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        // ユーザー作成
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info("管理者ユーザーを作成しました！");
        $this->table(
            ['ID', '名前', 'メールアドレス', '管理者'],
            [[$user->id, $user->name, $user->email, '✓']]
        );

        $this->newLine();
        $this->info("管理画面: " . url('/admin'));

        return self::SUCCESS;
    }
}
