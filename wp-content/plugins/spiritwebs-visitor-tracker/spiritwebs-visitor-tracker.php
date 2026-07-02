<?php
/*
Plugin Name: SpiritWebs Visitor Tracker
Description: Gửi thông tin khi có khách truy cập website đến hệ thống SpiritWebs (Phoenix Socket).
Version: 1.0
Author: SpiritWebs
*/

add_action('wp_footer', 'spiritwebs_send_visitor_info');

function spiritwebs_send_visitor_info() {
    ?>
    <script>
        (function () {
            if (window.spiritwebs_visitor_sent) return;
            window.spiritwebs_visitor_sent = true;

            const script = document.createElement("script");
            script.src = "https://cdn.jsdelivr.net/npm/phoenix@1.7.9/priv/static/phoenix.min.js";
            script.onload = () => {
                const socket = new window.Phoenix.Socket("wss://socket.spiritwebs.com/socket", {
                    params: {
                        type: "visitor",
                        source_site: window.location.hostname,
                        url: window.location.href,
                        timestamp: Date.now()
                    }
                });
                socket.connect();

                const channel = socket.channel("site:visit", {});
                channel.join()
                    .receive("ok", () => {
                    console.log("SpiritWebs: Visitor joined channel");
                channel.push("new_visitor", {
                    page: window.location.pathname,
                    referrer: document.referrer,
                    userAgent: navigator.userAgent
                });
            })
            .receive("error", resp => {
                    console.error("❌ Không thể join channel site:visit. Lỗi chi tiết:", resp);
            });

            };
            document.head.appendChild(script);
        })();
    </script>
    <?php
}
