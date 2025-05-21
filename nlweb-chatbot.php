<?php
/*
Plugin Name: NLWeb AI Chatbot
Description: Turns your site content into an AI chatbot using the NLWeb protocol. Just add the plugin and enter the NLWeb server URL. For detailed instructions, see https://github.com/nlweb-ai/quickstart.
Version: 1.0.0
Author: Kody Kendall
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Register settings
add_action('admin_init', 'nlweb_register_settings');
function nlweb_register_settings() {
    register_setting('nlweb_options_group', 'nlweb_server_url', array(
        'default' => 'http://localhost:8000',
        'sanitize_callback' => 'esc_url_raw'
    ));
}

// Add settings page
add_action('admin_menu', 'nlweb_add_settings_page');
function nlweb_add_settings_page() {
    add_options_page(
        'NLWeb Chatbot Settings',
        'NLWeb Chatbot',
        'manage_options',
        'nlweb-settings',
        'nlweb_render_settings_page'
    );
}


// Render settings page
function nlweb_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>NLWeb Chatbot Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('nlweb_options_group');
            do_settings_sections('nlweb-settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">NLWeb Server URL</th>
                    <td>
                        <input type="url" name="nlweb_server_url" value="<?php echo esc_attr(get_option('nlweb_server_url', 'http://localhost:8000')); ?>" class="regular-text" />
                        <p class="description">The URL of your NLWeb server (e.g., http://localhost:8000)</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('wp_footer', 'nlweb_inject_chatbot');
add_action('wp_ajax_nopriv_nlweb_query', 'nlweb_query_handler');
add_action('wp_ajax_nlweb_query', 'nlweb_query_handler');

function nlweb_query_handler() {
    // Verify and sanitize input
    if (!isset($_GET['query']) || !check_ajax_referer('nlweb_query_nonce', 'nonce', false)) {
        $error_response = json_encode(['message_type' => 'error', 'message' => 'Invalid request']);
        echo "data: " . esc_html($error_response) . "\n\n";
        exit;
    }
    
    $query = sanitize_text_field(wp_unslash($_GET['query']));
    if (empty($query)) {
        $error_response = json_encode(['message_type' => 'error', 'message' => 'Query is required']);
        echo "data: " . esc_html($error_response) . "\n\n";
        exit;
    }
    
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    
    $server_url = get_option('nlweb_server_url', 'http://localhost:8000');
    $url = trailingslashit($server_url) . "ask?query=" . urlencode($query) . "&generate_mode=summarize";
    
    // Set up non-blocking stream request
    $args = array(
        'timeout' => 0.01, // Very short timeout to return quickly
        'blocking' => false,
        'stream' => true,
        'filename' => null, // Stream to output
        'headers' => array(
            'Accept' => 'text/event-stream'
        )
    );
    
    // Open a connection to the NLWeb server
    $response = wp_remote_get($url, $args);
    
    // If there's an error, return it to the client
    if (is_wp_error($response)) {
        $error_response = json_encode(['message_type' => 'error', 'message' => $response->get_error_message()]);
        echo "data: " . esc_html($error_response) . "\n\n";
    }
    
    exit;
}

function nlweb_inject_chatbot() {
    // Generate a nonce for AJAX requests
    $nonce = wp_create_nonce('nlweb_query_nonce');
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        
        #nlweb-chat-button:hover {
            background: #0063b1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        #nlweb-chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 370px;
            height: 550px;
            background: white;
            border: none;
            border-radius: 16px;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 10000;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        #nlweb-chat-header {
            padding: 16px;
            background: #f8f8f8;
            font-weight: 600;
            border-bottom: 1px solid #eaeaea;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        #nlweb-chat-header-close {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        
        #nlweb-chat-header-close:hover {
            background: #eaeaea;
            color: #333;
        }

        #nlweb-chat-messages {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: #f5f7f9;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .nlweb-message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.5;
            position: relative;
            word-wrap: break-word;
        }
        
        .nlweb-user-message {
            align-self: flex-end;
            background: #0078d4;
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .nlweb-ai-message {
            align-self: flex-start;
            background: white;
            color: #333;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        #nlweb-chat-input {
            display: flex;
            border-top: 1px solid #eaeaea;
            padding: 12px;
            background: white;
        }

        #nlweb-chat-input input {
            flex: 1;
            border: 1px solid #dedede;
            border-radius: 24px;
            padding: 10px 16px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
        }
        
        #nlweb-chat-input input:focus {
            border-color: #0078d4;
        }

        #nlweb-chat-input button {
            border: none;
            background: #0078d4;
            color: white;
            border-radius: 24px;
            padding: 10px 18px;
            margin-left: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        #nlweb-chat-input button:hover {
            background: #0063b1;
        }
        
        #nlweb-typing-indicator {
            display: none;
            align-self: flex-start;
            background: #e6e6e6;
            border-radius: 18px;
            padding: 12px 16px;
            margin-top: 4px;
            font-size: 14px;
            color: #666;
        }
        
        .nlweb-typing-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #666;
            animation: nlwebTypingAnimation 1.4s infinite ease-in-out;
            margin-right: 2px;
        }
        
        .nlweb-typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .nlweb-typing-dot:nth-child(3) {
            animation-delay: 0.4s;
            margin-right: 0;
        }
        
        @keyframes nlwebTypingAnimation {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-4px); }
        }
    </style>

    <div id="nlweb-chat-container">
        <div id="nlweb-chat-header">
            <span>Ask me anything</span>
            <button id="nlweb-chat-header-close" onclick="nlwebToggleChat()">×</button>
        </div>
        <div id="nlweb-chat-messages">
            <div class="nlweb-message nlweb-ai-message">
                Hello! How can I help you today?
            </div>
        </div>
        <div id="nlweb-typing-indicator">
            <span class="nlweb-typing-dot"></span>
            <span class="nlweb-typing-dot"></span>
            <span class="nlweb-typing-dot"></span>
        </div>
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
            if (chat.style.display === 'flex') {
                document.getElementById('nlweb-chat-query').focus();
            }
        }

        function nlwebSendQuery() {
            const input = document.getElementById('nlweb-chat-query');
            const query = input.value.trim();
            if (!query) return;

            const messages = document.getElementById('nlweb-chat-messages');
            const userMsg = document.createElement('div');
            userMsg.className = 'nlweb-message nlweb-user-message';
            userMsg.textContent = query;
            messages.appendChild(userMsg);
            
            // Show typing indicator
            const typingIndicator = document.getElementById('nlweb-typing-indicator');
            typingIndicator.style.display = 'block';
            
            // Scroll to bottom
            messages.scrollTop = messages.scrollHeight;

            input.value = "";

            fetch("<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=nlweb_query&query=" + encodeURIComponent(query) + "&nonce=<?php echo esc_js($nonce); ?>", {
                headers: { Accept: "text/event-stream" },
            })
            .then(response => {
                const reader = response.body.getReader();
                const decoder = new TextDecoder("utf-8");
                let buffer = "";
                let aiMsg = null;

                function read() {
                    return reader.read().then(({ done, value }) => {
                        if (done) {
                            // Hide typing indicator when done
                            typingIndicator.style.display = 'none';
                            return;
                        }
                        
                        buffer += decoder.decode(value, { stream: true });
                        const lines = buffer.split("\n");

                        for (let line of lines) {
                            if (line.startsWith("data:")) {
                                const json = line.slice(5).trim();
                                try {
                                    const event = JSON.parse(json);
                                    if (event.message_type === "summary") {
                                        // Hide typing indicator
                                        typingIndicator.style.display = 'none';
                                        
                                        // Create message if it doesn't exist
                                        if (!aiMsg) {
                                            aiMsg = document.createElement('div');
                                            aiMsg.className = 'nlweb-message nlweb-ai-message';
                                            messages.appendChild(aiMsg);
                                        }
                                        
                                        aiMsg.textContent = event.message;
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
            })
            .catch(error => {
                // Hide typing indicator in case of error
                typingIndicator.style.display = 'none';
                
                const errorMsg = document.createElement('div');
                errorMsg.className = 'nlweb-message nlweb-ai-message';
                errorMsg.textContent = "Sorry, there was an error connecting to the AI service. Please try again later.";
                messages.appendChild(errorMsg);
                messages.scrollTop = messages.scrollHeight;
            });
        }
        
        // Allow sending message with Enter key
        document.getElementById('nlweb-chat-query').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                nlwebSendQuery();
            }
        });
    </script>
    <?php
}