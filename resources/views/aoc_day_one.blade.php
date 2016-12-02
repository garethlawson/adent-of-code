@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="canvas">
                    {!! $html !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.canvas > div').each(function (i) {
                var currentDiv = $(this);
                setTimeout(function() {
                    currentDiv.removeClass('hidden');
                }, (i + 1) * 100);
            });
        });
    </script>
@endsection