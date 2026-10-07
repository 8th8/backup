
<?php

session_start();


// =========================================================
// LANGUAGE
// =========================================================

$lang = $_SESSION["language"] ?? "ja";

$allowed_languages = [
    "ja",
    "en",
    "ko",
    "zh"
];

if (!in_array($lang, $allowed_languages, true)) {
    $lang = "ja";
}


// =========================================================
// LANGUAGE FILE
// =========================================================

$language_file = "../languages/" . $lang . ".php";

if (file_exists($language_file)) {

    $translations = require $language_file;
} else {

    $translations = require "../languages/ja.php";
}


// =========================================================
// CART
// =========================================================

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$cart_count = 0;

foreach ($_SESSION["cart"] as $item) {

    $cart_count += (int)($item["quantity"] ?? 0);
}

?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>
        <?php echo htmlspecialchars($translations["restaurant_name"] ?? "寿司 博多魚がし"); ?>
    </title>


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&family=Noto+Sans+KR:wght@400;500;600;700&family=Noto+Sans+SC:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- =====================================================
         CSS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css">

</head>


<body class="menu-page">


    <!-- =========================================================
     HEADER
========================================================= -->

    <header class="menu-header">

        <div class="menu-header-inner">


            <!-- Restaurant -->
            <div class="menu-brand">

                <div class="menu-brand-mark">
                    魚
                </div>

                <div class="menu-brand-text">

                    <strong>
                        寿司 博多魚がし
                    </strong>

                    <span>
                        SUSHI HAKATA UOGASHI
                    </span>

                </div>

            </div>


            <!-- Cart -->
            <a
                href="cart.php"
                class="header-cart"
                aria-label="Cart">

                <span class="cart-icon">
                    🛒
                </span>

                <span class="cart-count">
                    <?php echo $cart_count; ?>
                </span>

            </a>


        </div>

    </header>



    <!-- =========================================================
     MAIN
========================================================= -->

    <main class="menu-main">


        <!-- =====================================================
         WELCOME
    ====================================================== -->

        <section class="menu-welcome">

            <p class="menu-welcome-sub">
                ようこそ
            </p>

            <h1>
                お好きな料理を<br>
                お選びください
            </h1>

            <p class="menu-welcome-en">
                Please select your favorite dishes.
            </p>

        </section>



        <!-- =====================================================
         CATEGORY NAVIGATION
    ====================================================== -->

        <section class="category-section">

            <div
                id="category-list"
                class="category-list">

                <!-- JavaScript sẽ tạo category -->

            </div>

        </section>



        <!-- =====================================================
         MENU
    ====================================================== -->

        <section class="foods-section">


            <div class="section-heading">

                <div>

                    <span>
                        おすすめ
                    </span>

                    <h2 id="category-title">
                        すべて
                    </h2>

                </div>

            </div>


            <!-- Loading -->

            <div
                id="menu-loading"
                class="menu-loading">

                <div class="loading-circle"></div>

                <p>
                    メニューを読み込んでいます...
                </p>

            </div>


            <!-- Food list -->

            <div
                id="food-list"
                class="food-list">

            </div>


            <!-- No food -->

            <div
                id="menu-empty"
                class="menu-empty"
                style="display:none;">

                <div class="empty-icon">
                    🍣
                </div>

                <p>
                    現在、商品がありません。
                </p>

            </div>


        </section>


    </main>



    <!-- =========================================================
     CART BAR
========================================================= -->

    <a
        href="cart.php"
        class="bottom-cart">


        <div class="bottom-cart-left">

            <span class="bottom-cart-icon">
                🛒
            </span>

            <div>

                <strong>
                    カートを見る
                </strong>

                <span>
                    商品を確認する
                </span>

            </div>

        </div>


        <div class="bottom-cart-right">

            <span id="bottom-cart-count">
                <?php echo $cart_count; ?>
            </span>

            <span>
                →
            </span>

        </div>


    </a>



    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <script>
        const currentLanguage = <?php echo json_encode($lang); ?>;

        let menuData = [];

        let selectedCategory = "all";


        // =========================================================
        // GET LANGUAGE FIELD
        // =========================================================

        function getName(item) {

            const field = "name_" + currentLanguage;

            if (item[field]) {
                return item[field];
            }

            // fallback
            if (item.name_ja) {
                return item.name_ja;
            }

            if (item.name_en) {
                return item.name_en;
            }

            return "商品名なし";
        }


        // =========================================================
        // LOAD MENU
        // =========================================================

        async function loadMenu() {

            const loading =
                document.getElementById("menu-loading");

            const foodList =
                document.getElementById("food-list");

            const empty =
                document.getElementById("menu-empty");


            try {

                const response =
                    await fetch("../api/menu.php");


                if (!response.ok) {
                    throw new Error("Menu API Error");
                }


                const data =
                    await response.json();


                menuData = data;


                loading.style.display = "none";


                renderCategories(
                    data.categories || []
                );


                renderFoods(
                    data.foods || []
                );


            } catch (error) {

                console.error(error);


                loading.innerHTML = `

            <div class="menu-error">

                <div class="error-icon">
                    ⚠
                </div>

                <p>
                    メニューを読み込めませんでした。
                </p>

                <button
                    onclick="loadMenu()"
                    class="retry-button"
                >
                    もう一度試す
                </button>

            </div>

        `;

            }

        }


        // =========================================================
        // RENDER CATEGORY
        // =========================================================

        function renderCategories(categories) {

            const container =
                document.getElementById("category-list");


            container.innerHTML = "";


            // All button

            const allButton =
                document.createElement("button");

            allButton.className =
                "category-button active";

            allButton.dataset.category =
                "all";

            allButton.innerHTML = `

        <span class="category-icon">
            🍣
        </span>

        <span>
            すべて
        </span>

    `;


            allButton.addEventListener(
                "click",
                () => {

                    selectCategory("all");

                }
            );


            container.appendChild(allButton);


            // Categories

            categories.forEach(category => {


                const button =
                    document.createElement("button");


                button.className =
                    "category-button";


                button.dataset.category =
                    category.id;


                const categoryName =
                    getName(category);


                button.innerHTML = `

            <span class="category-icon">
                ${getCategoryIcon(categoryName)}
            </span>

            <span>
                ${escapeHtml(categoryName)}
            </span>

        `;


                button.addEventListener(
                    "click",
                    () => {

                        selectCategory(
                            String(category.id)
                        );

                    }
                );


                container.appendChild(button);

            });

        }


        // =========================================================
        // CATEGORY ICON
        // =========================================================

        function getCategoryIcon(name) {

            if (
                name.includes("寿司") ||
                name.includes("すし") ||
                name.includes("Sushi")
            ) {
                return "🍣";
            }


            if (
                name.includes("刺身") ||
                name.includes("Sashimi")
            ) {
                return "🐟";
            }


            if (
                name.includes("飲") ||
                name.includes("Drink")
            ) {
                return "🍵";
            }


            if (
                name.includes("酒") ||
                name.includes("酒類")
            ) {
                return "🍶";
            }


            if (
                name.includes("一品")
            ) {
                return "🍱";
            }


            return "🍽️";

        }


        // =========================================================
        // SELECT CATEGORY
        // =========================================================

        function selectCategory(categoryId) {

            selectedCategory =
                categoryId;


            document
                .querySelectorAll(".category-button")
                .forEach(button => {

                    button.classList.toggle(
                        "active",
                        button.dataset.category === categoryId
                    );

                });


            renderFoods(menuData.foods || []);

        }


        // =========================================================
        // RENDER FOODS
        // =========================================================

        function renderFoods(foods) {

            const container =
                document.getElementById("food-list");

            const empty =
                document.getElementById("menu-empty");


            container.innerHTML = "";


            let filteredFoods = foods;


            if (selectedCategory !== "all") {

                filteredFoods =
                    foods.filter(
                        food =>
                        String(food.category_id) ===
                        String(selectedCategory)
                    );

            }


            if (filteredFoods.length === 0) {

                empty.style.display =
                    "block";

                return;

            }


            empty.style.display =
                "none";


            filteredFoods.forEach(food => {

                container.appendChild(
                    createFoodCard(food)
                );

            });

        }


        // =========================================================
        // CREATE FOOD CARD
        // =========================================================

        function createFoodCard(food) {

            const card =
                document.createElement("article");


            card.className =
                "food-card";


            const name =
                getName(food);


            const description =
                food.description || "";


            const price =
                Number(food.price || 0);


            const image =
                food.image ?
                "../images/" + food.image :
                "../images/no-image.png";


            card.innerHTML = `

        <div class="food-image-wrapper">

            <img
                src="${escapeHtml(image)}"
                alt="${escapeHtml(name)}"
                class="food-image"
                loading="lazy"
                onerror="this.src='../images/no-image.png'"
            >

        </div>


        <div class="food-info">

            <h3 class="food-name">
                ${escapeHtml(name)}
            </h3>


            ${
                description
                    ? `
                        <p class="food-description">
                            ${escapeHtml(description)}
                        </p>
                    `
                    : ""
            }


            <div class="food-bottom">


                <div class="food-price">

                    <span>
                        ¥
                    </span>

                    ${price.toLocaleString("ja-JP")}

                </div>


                <button
                    class="add-food-button"
                    onclick="addToCart(${food.id})"
                    aria-label="${escapeHtml(name)}をカートに追加"
                >

                    <span>
                        ＋
                    </span>

                </button>


            </div>

        </div>

    `;


            return card;

        }


        // =========================================================
        // ADD TO CART
        // =========================================================

        async function addToCart(foodId) {

            try {

                const response =
                    await fetch("../api/orders.php", {

                        method: "POST",

                        headers: {
                            "Content-Type": "application/json"
                        },

                        body: JSON.stringify({
                            action: "add_to_cart",
                            food_id: foodId,
                            quantity: 1
                        })

                    });


                /*
                 * 現在の orders API が
                 * 注文確定用の場合は、
                 * 後で cart 用 API に変更する。
                 *
                 * 今は UI 側だけ準備。
                 */


                if (response.ok) {

                    updateCartCount();

                }

            } catch (error) {

                console.error(error);

            }

        }


        // =========================================================
        // UPDATE CART COUNT
        // =========================================================

        async function updateCartCount() {

            try {

                const response =
                    await fetch("../api/menu.php");


                /*
                 * 実際のカート数取得は
                 * cart API 作成時に変更する。
                 */

            } catch (error) {

                console.error(error);

            }

        }


        // =========================================================
        // ESCAPE HTML
        // =========================================================

        function escapeHtml(value) {

            return String(value)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");

        }


        // =========================================================
        // START
        // =========================================================

        loadMenu();
    </script>


</body>

</html>
