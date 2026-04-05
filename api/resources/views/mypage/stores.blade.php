@extends('layouts.app')

@section('title', '行ったお店 - マイページ')

@section('content')
<div class="mypage">
    <style>
        .mypage {
            min-height: 100vh;
            background: var(--bg-base, #fbf3e8);
            padding: 24px 16px 48px;
        }

        .mypage-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .mypage-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-sub);
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .mypage-back:hover {
            color: var(--brand-main);
        }

        .mypage-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        .store-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .store-card {
            background: var(--card-bg, #fff);
            border-radius: var(--radius-md, 16px);
            padding: 20px;
            box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
        }

        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .store-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }

        .store-date {
            font-size: 12px;
            color: var(--text-sub);
        }

        .store-memo {
            background: var(--bg-soft);
            border-radius: var(--radius-sm, 8px);
            padding: 12px;
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 12px;
            white-space: pre-wrap;
        }

        .store-memo-empty {
            color: var(--text-sub);
            font-style: italic;
        }

        .store-actions {
            display: flex;
            gap: 8px;
        }

        .store-btn {
            flex: 1;
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
            cursor: pointer;
            border: none;
            transition: all var(--transition-normal, 200ms ease-out);
        }

        .store-btn-edit {
            background: var(--bg-soft);
            color: var(--text-main);
        }

        .store-btn-edit:hover {
            background: var(--line-soft);
        }

        .store-btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .store-btn-delete:hover {
            background: #fecaca;
        }

        .store-btn-view {
            background: var(--brand-main);
            color: #fff;
            text-decoration: none;
        }

        .store-btn-view:hover {
            opacity: 0.9;
        }

        .store-empty {
            text-align: center;
            padding: 48px 24px;
            background: var(--card-bg);
            border-radius: var(--radius-md, 16px);
        }

        .store-empty-icon {
            display: flex;
            justify-content: center;
            color: var(--brand-main, #9c3f2e);
            margin-bottom: 16px;
        }

        .store-empty-text {
            color: var(--text-sub);
        }

        /* 編集モーダル */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            width: 100%;
            max-width: 400px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .modal-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--line-soft);
            border-radius: var(--radius-sm, 8px);
            font-size: 14px;
            margin-bottom: 12px;
            resize: vertical;
            min-height: 100px;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--brand-main);
        }

        .modal-date {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--line-soft);
            border-radius: var(--radius-sm, 8px);
            font-size: 14px;
            margin-bottom: 16px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            border-radius: var(--radius-sm, 8px);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .modal-btn-cancel {
            background: var(--bg-soft);
            color: var(--text-main);
        }

        .modal-btn-save {
            background: var(--brand-main);
            color: #fff;
        }

        .pagination-wrap {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }
    </style>

    <div class="mypage-container">
        <a href="{{ route('mypage') }}" class="mypage-back">← マイページに戻る</a>
        <h1 class="mypage-title"><x-svg-icon name="store" size="22" /> 行ったお店</h1>

        @if(session('success'))
            <div style="background: #dcfce7; color: #166534; padding: 12px; border-radius: 12px; margin-bottom: 16px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        @if($stores->count() > 0)
            <div class="store-list">
                @foreach($stores as $store)
                    <div class="store-card">
                        <div class="store-header">
                            <div class="store-name">{{ $store->name }}</div>
                            <div class="store-date">
                                {{ $store->pivot->visited_at ? \Carbon\Carbon::parse($store->pivot->visited_at)->format('Y/m/d') : '日付未設定' }}
                            </div>
                        </div>

                        <div class="store-memo">
                            @if($store->pivot->memo)
                                {{ $store->pivot->memo }}
                            @else
                                <span class="store-memo-empty">メモはありません</span>
                            @endif
                        </div>

                        <div class="store-actions">
                            <a href="{{ route('store.detail', $store->id) }}" class="store-btn store-btn-view">詳細を見る</a>
                            <button type="button" class="store-btn store-btn-edit" onclick="openEditModal({{ $store->id }}, '{{ addslashes($store->pivot->memo ?? '') }}', '{{ $store->pivot->visited_at ?? '' }}')">
                                編集
                            </button>
                            <form action="{{ route('mypage.stores.remove', $store->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('本当に削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="store-btn store-btn-delete" style="width: 100%;">削除</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination-wrap">
                {{ $stores->links() }}
            </div>
        @else
            <div class="store-empty">
                <div class="store-empty-icon"><x-svg-icon name="store" size="48" /></div>
                <p class="store-empty-text">まだ行ったお店がありません<br>診断結果からお店を追加できます</p>
            </div>
        @endif
    </div>

    {{-- 編集モーダル --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <div class="modal-title">メモを編集</div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <textarea name="memo" class="modal-input" id="editMemo" placeholder="お店の感想やメモを書いてね..."></textarea>
                <input type="date" name="visited_at" class="modal-date" id="editDate">
                <div class="modal-actions">
                    <button type="button" class="modal-btn modal-btn-cancel" onclick="closeEditModal()">キャンセル</button>
                    <button type="submit" class="modal-btn modal-btn-save">保存</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(storeId, memo, date) {
            document.getElementById('editForm').action = '/mypage/stores/' + storeId;
            document.getElementById('editMemo').value = memo;
            document.getElementById('editDate').value = date;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // モーダル外クリックで閉じる
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</div>
@endsection
