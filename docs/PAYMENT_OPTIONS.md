# 5akeMe に決済を入れるなら（選択肢メモ）

## 結論：まずは Stripe がおすすめ

- **Laravel と相性が良い**（公式 Laravel Cashier で一括・サブスク対応）
- **月額・初期費用なし**、使った分だけ 3.6%
- **日本対応**（クレカ・コンビニ・銀行振込など）
- **ドキュメント・API が整っている**ので実装が早い

---

## 主な選択肢の比較

| サービス | 手数料（目安） | 月額 | 特徴 | 向いているケース |
|----------|----------------|------|------|-------------------|
| **Stripe** | 3.6%／件 | なし | API 充実、Laravel Cashier 対応、コンビニ・銀行振込も可 | **オンライン・サブスク・API連携** |
| **Square** | オンライン 3.6%、対面 2.5% | なし | 対面決済が安い、翌営業日入金 | 実店舗の会計も一緒にやりたいとき |
| **PayPal** | 3.6% + 40円／件 | なし | 「PayPalで支払う」で認知度高 | 海外顧客が多い越境EC（少額だと割高） |
| **GMO PG** | 3.95% + 数円など | 要問合せ | 国内大手、複数決済手段 | 大規模EC・既存GMO契約あり |
| **STORES 決済** | 要確認 | なし | 初期費用なし、シンプル | ストア型・小規模 |

※手数料・条件は公式最新を確認してください。

---

## サケミーで想定しやすいパターン

1. **プレミアム機能の月額課金**（例：おすすめ店舗の詳細を見る）
   → **Stripe + Laravel Cashier** でサブスク実装がしやすい。

2. **単発の課金**（例：診断の有料版・チケット購入）
   → **Stripe Checkout** や **Payment Links** で「リンク1つで決済ページ」も可能。

3. **提携店舗の決済もまとめてやりたい**
   → 対面が多いなら **Square** を併用検討。オンラインだけなら Stripe で十分。

4. **コンビニ・銀行振込を必須にしたい**
   → Stripe でも日本でコンビニ・銀行振込に対応。それで足りなければ GMO PG 等を検討。

---

## Stripe を入れるときの流れ（イメージ）

1. [Stripe](https://stripe.com/jp) でアカウント作成（日本選択）
2. `composer require laravel/cashier` で Laravel Cashier 導入
3. `.env` に `STRIPE_KEY` / `STRIPE_SECRET` を設定
4. 一回払いなら Checkout Session や Payment Link、月額なら Cashier のサブスク API を使用
5. Webhook で「決済完了」を受け取り、DB の権限や状態を更新

詳細は [Laravel Billing（Cashier）](https://laravel.com/docs/billing) と [Stripe ドキュメント](https://stripe.com/docs) を参照。

---

## 参考リンク

- [Stripe 料金（日本）](https://stripe.com/jp/pricing)
- [Laravel Cashier（Stripe）](https://laravel.com/docs/billing)
- [Stripe で受け取れる支払い方法（日本）](https://stripe.com/jp/payments/payment-methods)
