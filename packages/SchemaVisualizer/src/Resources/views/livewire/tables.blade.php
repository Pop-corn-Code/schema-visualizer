<div class="p-6">
    <h1 class="text-3xl font-bold mb-6">Database Tables</h1>

    @if($tables)
        <div class="mb-6">
            {!! $this->generateMermaidClassDiagram() !!}
        </div>
    @else
        <div class="text-gray-500">No tables found.(Please run migration to refresh)</div>
    @endif
</div>

