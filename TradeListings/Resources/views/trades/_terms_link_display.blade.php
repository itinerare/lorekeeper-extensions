@if (isset($trade->terms_link) && $trade->terms_link)
    <div class="row">
        <div class="col-md-2 col-4">
            <h5>Proof of Terms</h5>
        </div>
        <div class="col-md-10 col-8"><a href="{{ $trade->terms_link }}">{{ $trade->terms_link }}</a></div>
    </div>
@endif
