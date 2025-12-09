<?php include './templates/header.php'; ?>

<section id="conversations">
    <aside>
        Liste des conversations
        <ul id="conversation-list">

        </ul>
    </aside>
    <article>
        <ul id="messages-list"></ul>
        <div>
            <input type="text" name="message" id="message" placeholder="Tapez votre message ici" />
            <button id="send-message">Envoyer</button>
        </div>
    </article>
</section>

<script type="module">
  import { getAllConversations, getAllMessagesForConversation, createMessageForConversation } from "./services/conversations.js";
  import { getConnectedUser } from './services/profile.js';


  document.addEventListener("DOMContentLoaded", () => {
    Promise.all([
        getAllConversations(),
        getConnectedUser()
    ])
    .then((values) => {
        const conversations = values[0];
        const connectedUser = values[1];
        const convList = document.getElementById('conversation-list');
        conversations.forEach(conv => {
            const elem = document.createElement('li');
            elem.innerText = conv.id; // TODO récupérer les users de la conv' pour créer le titre
            elem.setAttribute('id', 'conv-' + conv.id);
            convList.appendChild(elem);
        });

        const selectedConv = new URLSearchParams(window.location.search).get('conversation_id');

        if(selectedConv) {
            const elemSelected = document.getElementById("conv-" + selectedConv);
            elemSelected.classList.add('active');

            getAllMessagesForConversation(selectedConv).then((messages) => {
                const messageList = document.getElementById('messages-list');
                messages.forEach(message => {
                    const messageElem = document.createElement('li');
                    messageElem.innerText = message.content;
                    if(message.sender_id === connectedUser.id) {
                        messageElem.classList.add('me');
                    }
                    messageList.appendChild(messageElem);
                });
            });
        }
    });

    document.getElementById("send-message").addEventListener("click", () => {
        const message = document.getElementById('message').value.trim();
        const selectedConv = new URLSearchParams(window.location.search).get('conversation_id');

        if (message && selectedConv) {
            createMessageForConversation(selectedConv, message).then((m) => {
                const messageList = document.getElementById('messages-list');
                const messageElem = document.createElement('li');
                messageElem.innerText = message;
                messageElem.classList.add('me');
                messageList.appendChild(messageElem);
                document.getElementById('message').value = '';
            }).catch((err) => {
                console.error(err)
            });
        }
    });
  });
</script>

<?php include './templates/footer.php'; ?>