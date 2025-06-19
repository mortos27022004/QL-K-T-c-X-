<?php
// message.php
require_once 'config.php';

$current_user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        c.id AS conversation_id,
        u.user_id,
        u.name AS partner_name,
        m.message,
        m.created_at
    FROM conversations c
    JOIN user u ON (u.user_id = IF(c.user1_id = ?, c.user2_id, c.user1_id))
    LEFT JOIN (
        SELECT conversation_id, message, created_at
        FROM messages
        WHERE id IN (
            SELECT MAX(id) FROM messages GROUP BY conversation_id
        )
    ) m ON m.conversation_id = c.id
    WHERE c.user1_id = ? OR c.user2_id = ?
    ORDER BY m.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $current_user_id, $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="d-flex">
    <div class="sidebar">
        <div class="p-3 border-bottom">
            <h5 class="mb-0">Tin nhắn</h5>
        </div>

        <div class="chat-list d-flex">
            <?php while ($row = $result->fetch_assoc()): ?>
                <a href="<?php
                    echo ($_SESSION['role'] !== 'landlord')
                        ? "index.php?page_layout=message&conversation_id=" . $row['conversation_id']
                        : "host.php?page_layout=message&conversation_id=" . $row['conversation_id'];
                ?>" class="chat-list-item d-flex align-items-start p-2 border-bottom flex-grow-1">
                    <img src="https://via.placeholder.com/40" class="chat-avatar me-3 rounded-circle align-self-center" alt="Avatar">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div class="chat-name fw-bold"><?= htmlspecialchars($row['partner_name']) ?></div>
                            <div class="chat-time text-muted" style="font-size: 0.875em;">
                                <?= $row['created_at'] ? date('H:i', strtotime($row['created_at'])) : '' ?>
                            </div>
                        </div>
                        <div class="chat-text text-truncate">
                            <?= htmlspecialchars($row['message']) ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if (isset($_GET['conversation_id'])):
        $conversation_id = (int) $_GET['conversation_id'];

        $stmt_msg = $conn->prepare("
            SELECT m.*, u.name 
            FROM messages m 
            JOIN user u ON m.sender_id = u.user_id
            WHERE m.conversation_id = ?
            ORDER BY m.created_at ASC
        ");
        $stmt_msg->bind_param("i", $conversation_id);
        $stmt_msg->execute();
        $msg_result = $stmt_msg->get_result();
    ?>
    <div class="d-flex flex-column flex-grow-1 h-100">
        <div id="chat-content" class="chat-content p-3 flex-grow-1 overflow-auto" style="background-color: #f8f9fa;">
            <?php while ($msg = $msg_result->fetch_assoc()): ?>
                <div class="mb-3 d-flex <?= $msg['sender_id'] == $current_user_id ? 'justify-content-end' : 'justify-content-start' ?>">
                    <div class="<?= $msg['sender_id'] == $current_user_id ? 'bg-primary text-white' : 'bg-light' ?> p-3 rounded-3 shadow-sm" style="max-width: 70%;">
                        <div class="fw-bold mb-1"><?= htmlspecialchars($msg['name']) ?></div>
                        <div><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
                        <div class="text-end mt-1">
                            <small class="<?= $msg['sender_id'] == $current_user_id ? 'text-light' : 'text-muted' ?>">
                                <?= date('H:i d/m/Y', strtotime($msg['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <form id="chat-form" method="POST" class="border-top d-flex p-3 bg-white">
            <input type="hidden" id="conversation_id" name="conversation_id" value="<?= $conversation_id ?>">
            <input type="text" id="message-input" name="message" class="form-control me-2" placeholder="Nhập tin nhắn..." required>
            <button type="submit" class="btn btn-primary">Gửi</button>
        </form>
    </div>
    <?php else: ?>
        <div class="d-flex flex-grow-1 align-items-center justify-content-center text-muted">
            <p class="fs-5">Chọn một đoạn chat để bắt đầu</p>
        </div>
    <?php endif; ?>
</div>

<script>
const socket = new WebSocket("ws://localhost:8080");

socket.onopen = () => {
    socket.send(JSON.stringify({
        type: 'init',
        conversation_id: <?= (int) ($_GET['conversation_id'] ?? 0) ?>
    }));
};

socket.onmessage = function (event) {
    const data = JSON.parse(event.data);
    const isSelf = data.sender_id === <?= (int) $_SESSION['user_id'] ?>;
    const chatContent = document.getElementById('chat-content');

    const messageDiv = document.createElement('div');
    messageDiv.className = 'mb-3 d-flex ' + (isSelf ? 'justify-content-end' : 'justify-content-start');

    messageDiv.innerHTML = `
        <div class="${isSelf ? 'bg-primary text-white' : 'bg-light'} p-3 rounded-3 shadow-sm" style="max-width: 70%;">
            <div>${data.message}</div>
            <div class="text-end mt-1">
                <small class="${isSelf ? 'text-light' : 'text-muted'}">${data.created_at}</small>
            </div>
        </div>
    `;

    chatContent.appendChild(messageDiv);
    chatContent.scrollTop = chatContent.scrollHeight;
};

// Gửi tin nhắn
const form = document.querySelector('#chat-form');
form.addEventListener('submit', function (e) {
    e.preventDefault();
    const input = this.querySelector('input[name="message"]');
    const msg = input.value.trim();
    if (!msg) return;

    const messageData = {
        conversation_id: <?= (int) $_GET['conversation_id'] ?? 0 ?>,
        sender_id: <?= (int) $_SESSION['user_id'] ?>,
        message: msg
    };

    socket.send(JSON.stringify(messageData));
    input.value = '';
});
</script>
