<section class="dr-stores-section" id="stores-section">
    <h2 class="dr-stores-title">
        <span class="dr-stores-icon"><x-svg-icon name="wine" size="22" /></span>
        佐賀駅周辺のおすすめ店舗
    </h2>
    <p class="dr-stores-subtitle">あなたの診断結果にぴったりのお店を厳選しました</p>

    @if(isset($stores) && $stores->count() > 0)
        <div class="dr-stores-list">
            @foreach($stores as $store)
                <div class="dr-store-card">
                    <div class="dr-store-header">
                        <h3 class="dr-store-name">{{ $store->name }}</h3>
                        <span class="dr-store-mood">
                            @if($store->mood === 'lively')
                                🎉 にぎやか
                            @elseif($store->mood === 'calm')
                                🌙 落ち着き
                            @else
                                ✨ 両方OK
                            @endif
                        </span>
                    </div>

                    <div class="dr-store-info">
                        @if($store->address)
                            <div class="dr-store-row">
                                <span class="dr-store-label"><x-svg-icon name="map-pin" size="14" /></span>
                                <span>{{ $store->address }}</span>
                            </div>
                        @endif

                        @if($store->business_hours)
                            <div class="dr-store-row">
                                <span class="dr-store-label"><x-svg-icon name="clock" size="14" /></span>
                                <span>{{ $store->business_hours }}</span>
                            </div>
                        @endif

                        @if($store->closed_days)
                            <div class="dr-store-row">
                                <span class="dr-store-label"><x-svg-icon name="calendar" size="14" /></span>
                                <span>定休日: {{ $store->closed_days }}</span>
                            </div>
                        @endif
                    </div>

                    @php
                        $displaySakeTypes = $store->sake_types ? array_values(array_diff($store->sake_types, \App\Models\Store::getDisplayOnlyTags())) : [];
                    @endphp
                    @if(count($displaySakeTypes) > 0)
                        <div class="dr-store-tags">
                            @foreach(array_slice($displaySakeTypes, 0, 3) as $type)
                                <span class="dr-store-tag">{{ \App\Models\Store::getSakeTypeLabel($type) }}</span>
                            @endforeach
                            @if(count($displaySakeTypes) > 3)
                                <span class="dr-store-tag">+{{ count($displaySakeTypes) - 3 }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="dr-store-actions">
                        <a href="{{ route('store.detail', $store->id) }}" class="dr-store-btn dr-store-btn-detail">
                            詳細を見る →
                        </a>
                        @if($store->address)
                            <a 
                                href="https://www.google.com/maps/search/?api=1&query={{ urlencode($store->address) }}" 
                                target="_blank" 
                                rel="noopener noreferrer" 
                                class="dr-store-btn dr-store-btn-map"
                            >
                                <x-svg-icon name="map-pin" size="14" /> MAP
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="dr-stores-empty">
            <p>現在、おすすめ店舗の情報を準備中です。</p>
        </div>
    @endif

    <p class="dr-stores-note">
        ※ 店舗情報は変更される場合があります。お出かけ前にご確認ください。
    </p>
</section>
