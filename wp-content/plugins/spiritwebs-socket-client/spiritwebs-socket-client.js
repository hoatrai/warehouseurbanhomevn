(function waitForUserData(retry = 0) {
    const d = window?.spiritwebs_user_data;
    if (!d || !d.username) {
        if (retry < 20) return setTimeout(() => waitForUserData(retry + 1), 200);
        console.warn("⛔ Không thấy spiritwebs_user_data sau đợi");
        return;
    }

    const { username, email } = d;
    const site = location.hostname;
    const loginKey = "spiritwebs_user_login_sent";

    function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
        document.cookie = `${name}=${value}; path=/; expires=${d.toUTCString()}`;
    }
    function getCookie(name) {
        const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
        return match ? match[2] : null;
    }

    if (location.href.includes("action=logout")) {
        setCookie(loginKey, "", -1);
        console.log("🧼 Đã logout, xoá loginKey");

        // --- Clear user online list ---
        if (window.swOnlineUsers) {
            Object.keys(window.swOnlineUsers).forEach(key => {
                const li = document.querySelector(`#${CSS.escape("online-user-" + key)}`);
            if (li) li.remove();
        });
            window.swOnlineUsers = {};
        }

        const countEl = document.querySelector("#sw-online-count");
        if (countEl) countEl.innerText = "0";

        return;
    }


    const isSent = getCookie(loginKey) === "1";

    // Load Phoenix.js từ CDN
    const script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/phoenix@1.7.9/priv/static/phoenix.min.js";
    script.onload = () => {
        const socket = new Phoenix.Socket("wss://socket.okinawanew.com/socket", { params: { username, email } });
        socket.connect();

        // --- room:lobby gửi user_login / user_active ---
        const roomChannel = socket.channel("room:lobby", {});
        roomChannel.join().receive("ok", () => {
            const data = () => ({
            site,
            username,
            email,
            time: new Date().toISOString(),
        });

        if (!isSent) {
            roomChannel.push("user_login", data());
            console.log("✅ Send user_login");
            setCookie(loginKey, "1", 1);
        } else {
            console.log("✔️ Cookie already exists → do not send user_login");
        }
        roomChannel.push("user_active", data());
        console.log("📡 Send user_active (1 time when joining)");


        /*setInterval(() => {
            roomChannel.push("user_active", data());
        console.log("📡 Gửi user_active");
    }, 10000);*/
    });

        // --- online:lobby hiển thị user online ---
        const onlineChannel = socket.channel("online:lobby", {});
        onlineChannel.join()
            .receive("ok", () => console.log("✅ Joined online:lobby"))
    .receive("error", () => console.error("❌ Failed to join online:lobby"));

        onlineChannel.on("presence_state", state => {
            renderOnlineUsers(state);
    });

        onlineChannel.on("presence_diff", diff => {
            applyDiff(diff);
    });

        // --- quản lý menu và realtime update ---
        if (!window.swOnlineUsers) window.swOnlineUsers = {};

        function renderOnlineUsers(state) {
            const parent = document.querySelector("#wp-admin-bar-online-users-submenu");
            if (!parent) return;

            let menu = parent.querySelector(".ab-submenu");
            if (!menu) {
                menu = document.createElement("ul");
                menu.className = "ab-submenu";
                menu.style.margin = "0";
                menu.style.padding = "0";
                parent.appendChild(menu);
            }

            menu.innerHTML = "";
            window.swOnlineUsers = {};

            Object.entries(state).forEach(([key, pres]) => {
                const user = pres.metas[0];
            const li = document.createElement("li");
            li.id = "online-user-" + key;
            li.innerHTML = `<a class="ab-item" href="/wp-admin/user-edit.php?user_id=${user.id}">${user.username} (0 phút 0s trước)</a>`;
            menu.appendChild(li);

            window.swOnlineUsers[key] = {
                username: user.username,
                id: user.id,
                online_at: user.online_at * 1000
            };
        });

            updateOnlineCount();
        }

        function applyDiff(diff) {
            const menu = document.querySelector("#wp-admin-bar-online-users-submenu .ab-submenu");
            if (!menu) return;

            // join
            Object.entries(diff.joins || {}).forEach(([key, pres]) => {
                if (window.swOnlineUsers[key]) return;
            const user = pres.metas[0];
            const li = document.createElement("li");
            li.id = "online-user-" + key;
            li.innerHTML = `<a class="ab-item" href="/wp-admin/user-edit.php?user_id=${user.id}">${user.username} (0 phút 0s trước)</a>`;
            menu.appendChild(li);

            window.swOnlineUsers[key] = {
                username: user.username,
                id: user.id,
                online_at: user.online_at * 1000
            };
        });

            // leave
            Object.keys(diff.leaves || {}).forEach(key => {
                const li = document.querySelector(`#${CSS.escape("online-user-" + key)}`);
            if (li) li.remove();
            delete window.swOnlineUsers[key];
        });


            updateOnlineCount();
        }

        function updateOnlineCount() {
            const countEl = document.querySelector("#sw-online-count");
            if (countEl) countEl.innerText = Object.keys(window.swOnlineUsers).length;
        }

        // --- realtime update số phút/giây ---
        setInterval(() => {
            const now = Date.now();
        Object.entries(window.swOnlineUsers).forEach(([key, user]) => {
            const li = document.querySelector(`#${CSS.escape("online-user-" + key)} a`);
        if (!li) return;

        const diffMs = now - user.online_at;
        const minutes = Math.floor(diffMs / 60000);
        const seconds = Math.floor((diffMs % 60000) / 1000);
        li.innerText = `${user.username} (${minutes} phút ${seconds}s trước)`;
    });
    }, 1000);



        // --- rows:lobby lắng nghe new_row ---
        const rowsChannel = socket.channel("rows:lobby", {});
        rowsChannel.join()
            .receive("ok", () => console.log("✅ Joined rows:lobby"))
    .receive("error", () => console.error("❌ Lỗi join rows:lobby"));

        rowsChannel.on("new_row", payload => {
            console.log("📥 Có dữ liệu mới, reload grid");
        if (typeof jQuery !== "undefined" && jQuery("#jqGrid").length) {
            window.justAdded = true;
            jQuery("#jqGrid").trigger("reloadGrid");
        }
    });
    };

    document.body.appendChild(script);
})();
