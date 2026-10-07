```sql
/* =========================================================
   CATEGORIES
========================================================= */

INSERT INTO categories
    (name_ja, name_en, name_ko, name_zh)
VALUES
    ('寿司', 'Sushi', '초밥', '寿司'),
    ('刺身', 'Sashimi', '회', '刺身'),
    ('一品料理', 'Side Dishes', '일품요리', '单品料理'),
    ('飲み物', 'Drinks', '음료', '饮料');


/* =========================================================
   FOODS
========================================================= */


/* ---------------------------------------------------------
   寿司
--------------------------------------------------------- */

INSERT INTO foods
    (
        category_id,
        name_ja,
        name_en,
        name_ko,
        name_zh,
        description,
        price,
        image,
        is_available
    )
VALUES
(
    1,
    'まぐろ',
    'Tuna',
    '참치',
    '金枪鱼',
    '新鮮なまぐろを使用した定番の一品',
    280,
    'maguro.png',
    1
),

(
    1,
    'サーモン',
    'Salmon',
    '연어',
    '三文鱼',
    '脂ののった人気のサーモン',
    250,
    'salmon.png',
    1
),

(
    1,
    'はまち',
    'Yellowtail',
    '방어',
    '油甘鱼',
    '新鮮なはまちの握り',
    300,
    'hamachi.png',
    1
),

(
    1,
    'えび',
    'Shrimp',
    '새우',
    '虾',
    'ぷりぷりのえびを使用',
    220,
    'ebi.png',
    1
),

(
    1,
    'いくら',
    'Salmon Roe',
    '연어알',
    '鲑鱼籽',
    'ぷちぷち食感のいくら',
    450,
    'ikura.png',
    1
);


/* ---------------------------------------------------------
   刺身
--------------------------------------------------------- */

INSERT INTO foods
    (
        category_id,
        name_ja,
        name_en,
        name_ko,
        name_zh,
        description,
        price,
        image,
        is_available
    )
VALUES
(
    2,
    'まぐろ刺身',
    'Tuna Sashimi',
    '참치회',
    '金枪鱼刺身',
    '新鮮なまぐろの刺身',
    680,
    'maguro_sashimi.png',
    1
),

(
    2,
    'サーモン刺身',
    'Salmon Sashimi',
    '연어회',
    '三文鱼刺身',
    '脂ののったサーモンの刺身',
    650,
    'salmon_sashimi.png',
    1
),

(
    2,
    '刺身盛り合わせ',
    'Sashimi Assortment',
    '모둠회',
    '刺身拼盘',
    'おすすめの刺身を盛り合わせました',
    1_280,
    'sashimi_moriawase.png',
    1
);


/* ---------------------------------------------------------
   一品料理
--------------------------------------------------------- */

INSERT INTO foods
    (
        category_id,
        name_ja,
        name_en,
        name_ko,
        name_zh,
        description,
        price,
        image,
        is_available
    )
VALUES
(
    3,
    'だし巻き玉子',
    'Japanese Omelette',
    '일본식 계란말이',
    '日式玉子烧',
    'だしの風味を楽しめるふわふわ玉子',
    480,
    'dashimaki.png',
    1
),

(
    3,
    '枝豆',
    'Edamame',
    '에다마메',
    '毛豆',
    'おつまみにおすすめ',
    350,
    'edamame.png',
    1
),

(
    3,
    '茶碗蒸し',
    'Steamed Egg Custard',
    '차완무시',
    '茶碗蒸',
    'なめらかな食感の茶碗蒸し',
    420,
    'chawanmushi.png',
    1
);


/* ---------------------------------------------------------
   飲み物
--------------------------------------------------------- */

INSERT INTO foods
    (
        category_id,
        name_ja,
        name_en,
        name_ko,
        name_zh,
        description,
        price,
        image,
        is_available
    )
VALUES
(
    4,
    '生ビール',
    'Draft Beer',
    '생맥주',
    '生啤酒',
    '冷たい生ビール',
    580,
    'beer.png',
    1
),

(
    4,
    '緑茶',
    'Green Tea',
    '녹차',
    '绿茶',
    'すっきりとした味わいの緑茶',
    250,
    'green_tea.png',
    1
),

(
    4,
    'ウーロン茶',
    'Oolong Tea',
    '우롱차',
    '乌龙茶',
    250,
    'oolong_tea.png',
    1
);
```
