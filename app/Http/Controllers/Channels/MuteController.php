<?php

namespace App\Http\Controllers\Channels;

use App\Http\Controllers\Controller;
use App\Http\Requests\Channels\MuteRequest;
use App\Models\Channel;
use App\Models\ChannelMute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MuteController extends Controller
{
    public function store(MuteRequest $request, Channel $channel): RedirectResponse
    {
        ChannelMute::create([
            'channel_id' => $request->boolean('global') ? null : $channel->id,
            'user_id' => $request->integer('user_id'),
            'muted_until' => now()->addMinutes($request->integer('minutes')),
            'reason' => $request->validated('reason'),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', __('ui.channels.muted_user'));
    }

    public function destroy(Request $request, ChannelMute $channelMute): RedirectResponse
    {
        $user = $request->user();
        $allowed = $channelMute->channel
            ? $user->can('moderate', $channelMute->channel)
            : ($user->isAdmin() || $user->isModerator());

        abort_unless($allowed, 403);

        $channelMute->update(['muted_until' => now()]);

        return back()->with('success', __('ui.channels.unmuted_user'));
    }
}
