<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->boolean('must_change_password')->default(true)->after('is_active');
        });

        // Generate username from email for existing users
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            $baseUsername = \Illuminate\Support\Str::before($user->email, '@');
            $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '_', $baseUsername);
            $username = $baseUsername;
            $counter = 1;
            while (\DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . '_' . $counter;
                $counter++;
            }
            \DB::table('users')->where('id', $user->id)->update([
                'username' => $username,
                'must_change_password' => false, // existing users don't need to change
            ]);
        }

        // Now make username required and unique
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'must_change_password']);
        });
    }
};
