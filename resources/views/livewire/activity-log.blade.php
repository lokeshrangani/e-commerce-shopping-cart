<div class="max-w-4xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4">Activity Log</h2>

    @if($activities->isEmpty())
        <p class="text-gray-500">No activity recorded yet.</p>
    @else
        <div class="space-y-3">
            @foreach ($activities as $activity)
                <div class="border rounded p-3 bg-white shadow-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ str_replace('.', ' ', ucfirst($activity->action)) }}
                            </p>

                            @if(!empty($activity->meta))
                                <pre class="text-xs text-gray-600 mt-1 bg-gray-50 p-2 rounded">
                                    {{ json_encode($activity->meta, JSON_PRETTY_PRINT) }}
                                </pre>
                            @endif
                        </div>

                        <span class="text-xs text-gray-500">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
