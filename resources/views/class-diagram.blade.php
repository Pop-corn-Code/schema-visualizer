<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Diagram</title>
</head>

<body>
    <div id="class-diagram">
        <div class="mermaid">
            {{ $diagram }}
        </div>
    </div>

    <!-- Add Mermaid.js from CDN -->
    {{-- <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        mermaid.initialize({ startOnLoad: true });
    </script> --}}

</body>

</html>
