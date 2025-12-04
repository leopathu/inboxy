<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@inboxy.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'timezone' => 'UTC',
        ]);

        // Create default brand
        $brand = Brand::create([
            'name' => 'Default Brand',
            'company' => 'Inboxy',
            'email' => 'contact@inboxy.local',
            'from_name' => 'Inboxy',
            'from_email' => 'noreply@inboxy.local',
            'primary_color' => '#3490dc',
            'monthly_send_limit' => 0, // Unlimited
            'is_active' => true,
            'can_create_lists' => true,
            'can_create_campaigns' => true,
            'can_import_subscribers' => true,
            'can_export_data' => true,
            'can_view_reports' => true,
        ]);

        // Attach admin to brand as owner
        $brand->users()->attach($admin->id, [
            'role' => 'owner',
            'can_manage_lists' => true,
            'can_manage_campaigns' => true,
            'can_manage_subscribers' => true,
            'can_view_reports' => true,
            'can_manage_settings' => true,
            'can_manage_users' => true,
        ]);

        // Set as current brand
        $admin->update(['current_brand_id' => $brand->id]);

        $this->command->info('Admin user created: admin@inboxy.local / password');
        $this->command->info('Default brand created and assigned to admin.');
    }
}
