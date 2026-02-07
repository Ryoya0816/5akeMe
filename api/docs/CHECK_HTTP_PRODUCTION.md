# HTTP 本番で動くかチェック

IP 直叩き（http://160.251.214.119）や、ドメインでまだ HTTPS にしていないとき用です。

---

## コード側で対応済み

| 項目 | 内容 |
|------|------|
| **年齢確認 Cookie** | `AgeCheckController`: Secure を `$request->secure()` に変更済み。HTTP では Cookie が保存される。 |
| **HSTS** | `SecurityHeaders`: HTTPS のときだけ `Strict-Transport-Security` を付与。HTTP では付与しない。 |

---

## サーバ .env で確認すること（HTTP 運用時）

| 変数 | 推奨値 | 理由 |
|------|--------|------|
| `SESSION_SECURE_COOKIE` | 未設定 または `false` | true だとセッション Cookie が HTTP で送られずログイン等が動かない |
| `DB_CONNECTION` | `mysql` | sqlite のままだと readonly 等で 500 になる |
| `SESSION_DRIVER` | `file` | DB 書き込みを避ける |

---

## 動作確認の流れ

1. **トップ** … http://160.251.214.119/ → welcome → 年齢確認へ
2. **年齢確認** … 「はい」→ Cookie 保存 → /top へリダイレクト
3. **/top** … 年齢確認済みとして表示
4. **ログイン** … ログイン → ダッシュボード（マイページ）
5. **診断** … /diagnose → 質問 → 結果表示
6. **ログアウト** … 問題なく終了

HTTPS にしたあとは、`SESSION_SECURE_COOKIE=true` を設定し、年齢確認 Cookie も Secure で保存されます（コードは `$request->secure()` で判定済み）。
