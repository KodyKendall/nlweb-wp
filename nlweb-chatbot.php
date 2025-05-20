<?php
/*
Plugin Name: NLWeb Chatbot
Description: Adds a floating AI chatbot to your WordPress site, powered by your NLWeb server.
Version: 1.0
Author: You
*/

add_action('wp_footer', 'nlweb_inject_chatbot');

function nlweb_inject_chatbot() {
    ?>
    <style>
        #nlweb-chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #0078d4;
            color: white;
            border: none;
            border-radius: 999px;
            padding: 12px 16px;
            font-size: 16px;
            cursor: pointer;
            z-index: 10000;
        }

        #nlweb-chat-container {
            position: fixed;
            bottom: 70px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 12px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 10000;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        #nlweb-chat-header {
            padding: 10px;
            background: #f3f3f3;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
        }

        #nlweb-chat-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            font-size: 14px;
        }

        #nlweb-chat-input {
            display: flex;
            border-top: 1px solid #ccc;
        }

        #nlweb-chat-input input {
            flex: 1;
            border: none;
            padding: 10px;
            font-size: 14px;
        }

        #nlweb-chat-input button {
            border: none;
            background: #0078d4;
            color: white;
            padding: 10px 16px;
            cursor: pointer;
        }
    </style>

    <div id="nlweb-chat-container">
        <div id="nlweb-chat-header">Ask me anything</div>
        <div id="nlweb-chat-messages"></div>
        <div id="nlweb-chat-input">
            <input type="text" id="nlweb-chat-query" placeholder="Ask a question..." />
            <button onclick="nlwebSendQuery()">Send</button>
        </div>
    </div>

    <button id="nlweb-chat-button" onclick="nlwebToggleChat()">💬 Chat</button>

    <script>
        function nlwebToggleChat() {
            const chat = document.getElementById('nlweb-chat-container');
            chat.style.display = (chat.style.display === 'none' || chat.style.display === '') ? 'flex' : 'none';
        }

        function nlwebSendQuery() {
            const input = document.getElementById('nlweb-chat-query');
            const query = input.value.trim();
            if (!query) return;

            const messages = document.getElementById('nlweb-chat-messages');
            const userMsg = document.createElement('div');
            userMsg.textContent = "You: " + query;
            messages.appendChild(userMsg);

            input.value = "";

            fetch("http://localhost:8000/ask?query=" + encodeURIComponent(query) + "&generate_mode=summarize", {
                headers: { Accept: "text/event-stream" },
            })
            .then(response => {
                const reader = response.body.getReader();
                const decoder = new TextDecoder("utf-8");
                let buffer = "";

                function read() {
                    return reader.read().then(({ done, value }) => {
                        if (done) return;
                        buffer += decoder.decode(value, { stream: true });
                        const lines = buffer.split("\n");

                        for (let line of lines) {
                            if (line.startsWith("data:")) {
                                const json = line.slice(5).trim();
                                try {
                                    const event = JSON.parse(json);
                                    if (event.message_type === "summary") {
                                        const aiMsg = document.createElement('div');
                                        aiMsg.textContent = "AI: " + event.message;
                                        messages.appendChild(aiMsg);
                                        messages.scrollTop = messages.scrollHeight;
                                    }
                                } catch (_) {}
                            }
                        }

                        buffer = lines[lines.length - 1];
                        return read();
                    });
                }

                read();
            });
        }
    </script>
    <?php
}