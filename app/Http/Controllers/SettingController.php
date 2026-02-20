<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the settings page.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Setting::class);

        $setting = Auth::user()->setting ?? new Setting();

        return Inertia::render('Settings/Index', [
            'setting' => $setting,
            'timezones' => timezone_identifiers_list(),
            'awsRegions' => $this->getAwsRegions(),
        ]);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'timezone' => 'required|string|max:255',
            'language' => 'required|string|max:10',
            'aws_access_key' => 'nullable|string|max:255',
            'aws_secret_key' => 'nullable|string|max:255',
            'aws_region' => 'nullable|string|max:255',
            'aws_sending_rate_limit' => 'required|integer|min:1|max:50',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        Auth::user()->setting()->updateOrCreate(
            ['user_id' => Auth::id()],
            $validated
        );

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    /**
     * Get list of AWS regions.
     */
    private function getAwsRegions(): array
    {
        return [
            'us-east-1' => 'US East (N. Virginia)',
            'us-east-2' => 'US East (Ohio)',
            'us-west-1' => 'US West (N. California)',
            'us-west-2' => 'US West (Oregon)',
            'af-south-1' => 'Africa (Cape Town)',
            'ap-east-1' => 'Asia Pacific (Hong Kong)',
            'ap-south-1' => 'Asia Pacific (Mumbai)',
            'ap-northeast-1' => 'Asia Pacific (Tokyo)',
            'ap-northeast-2' => 'Asia Pacific (Seoul)',
            'ap-northeast-3' => 'Asia Pacific (Osaka)',
            'ap-southeast-1' => 'Asia Pacific (Singapore)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'ca-central-1' => 'Canada (Central)',
            'eu-central-1' => 'Europe (Frankfurt)',
            'eu-west-1' => 'Europe (Ireland)',
            'eu-west-2' => 'Europe (London)',
            'eu-west-3' => 'Europe (Paris)',
            'eu-north-1' => 'Europe (Stockholm)',
            'eu-south-1' => 'Europe (Milan)',
            'me-south-1' => 'Middle East (Bahrain)',
            'sa-east-1' => 'South America (São Paulo)',
        ];
    }
}
