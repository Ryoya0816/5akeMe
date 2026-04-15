@if(!empty($snacks))
<section class="dr-pairing-section">
    <h2 class="dr-pairing-title">
        <span class="dr-pairing-icon"><x-svg-icon name="utensils" size="22" /></span>
        相性バツグンのおつまみ
    </h2>
    <p class="dr-pairing-subtitle">{{ $pairingLabel }}と一緒に楽しみたい料理</p>

    @if($catchCopy)
    <div class="dr-pairing-catch">
        「{{ $catchCopy }}」
    </div>
    @endif

    <div class="dr-pairing-list">
        @foreach($snacks as $snack)
        <div class="dr-pairing-item">
            <span class="dr-pairing-item-icon">🥢</span>
            <span>{{ $snack }}</span>
        </div>
        @endforeach
    </div>

    @if($onePhrase)
    <p class="dr-pairing-phrase">
        {{ $onePhrase }}
    </p>
    @endif
</section>
@endif
