# 店舗管理画面変更の影響チェック結果

## 変更内容（2025年2月）

1. **営業時間**: 開店・閉店を30分刻みのプルダウンで選択（2つのSelect）
2. **定休日**: プルダウンで選択
3. **お酒情報**: チェック方式に変更（日本酒辛口/甘口、焼酎麦/芋/米、生ビール、クラフトビール、ワイン赤/白/スパークリング、ウイスキー、ノンアル豊富、カクテル豊富、その他）

---

## 影響チェック結果

### ✅ 問題なし

| 箇所 | 内容 |
|------|------|
| **store_detail.blade.php** | `$store->business_hours` / `$store->closed_days` をそのまま表示。保存形式（例: `17:30〜24:00`）は変わらないため影響なし |
| **diagnose_result.blade.php** | 同上 |
| **StoreResource テーブル** | `business_hours` / `closed_days` を TextColumn で表示。形式は同じ |
| **DiagnoseController** | `whereJsonContains('sake_types', $primaryType)` で検索。診断の primary_type（sake_dry, craft_beer 等）は Store の sakeTypeOptions に含まれる |
| **config/diagnose.php** | 診断タイプ: sake_dry, sake_sweet, shochu_*, craft_beer, wine_*, cocktail, whisky → すべて Store の選択肢に存在 |
| **Python (scoring.py)** | 診断スコア計算。SAKE_TYPES は diagnose と同様で、Store のマッチングに使用する primary_type と一致 |
| **StoreReport** | 報告種別「営業時間」「定休日」はラベルのみ。表示・保存ロジックに変更なし |
| **DB スキーマ** | business_hours, closed_days, sake_types の型・カラムは変更なし |

### ⚠️ 既存データの注意点

| 状況 | 対応 |
|------|------|
| **古い sake_types** | `Store::getSakeTypeLabel()` で beer_lager, highball, sake_sparkling 等にもラベルを付与。店舗詳細・診断結果で正しく表示される。管理画面で新しい選択肢に更新可能 |
| **複雑な営業時間** | 「ランチ 11:00〜14:00 / ディナー 17:00〜23:00」等は `〜` で分割すると開店・閉店に正しくパースされない。編集時は開店・閉店を手動で選択し直す必要あり |
| **StoreSeeder** | 新形式（beer_draft, wine_sparkling 等）に更新済み |

### 佐賀の地酒タグ（saga_local_sake）

- **管理画面**: お酒情報のチェックに「佐賀の地酒」を追加
- **店舗詳細**: 赤く強調表示（`.store-sake-tag--saga`）
- **診断結果**: 表示しない（`getDisplayOnlyTags()` で除外）
- **診断マッチング**: 診断の primary_type に含まれないため、マッチングには使用されない

### 診断との整合性

- 診断の `primary_type` と Store の `sake_types` の対応:
  - sake_dry, sake_sweet ✓
  - shochu_mugi, shochu_imo, shochu_kome ✓
  - craft_beer ✓
  - wine_red, wine_white ✓
  - cocktail, whisky ✓
- Store のみのタイプ（beer_draft, wine_sparkling, non_alcohol, other）は診断では返らないが、店舗表示には問題なし

---

## 結論

**他箇所への影響はなく、変更は安全に適用できます。**
