<?php include './templates/header.php'; ?>

<section id="conversations">
  <aside>
    <h2 class="messaging-title">Messagerie</h2>
    <ul id="conversation-list"></ul>
  </aside>
  <article>
    <h2 id="conversation-title"></h2>
    <ul id="messages-list"></ul>
    <div class="message-input-bar">
      <label for="message" class="sr-only">Votre message</label>
      <input
        type="text"
        name="message"
        id="message"
        placeholder="Tapez votre message ici" />
      <button id="send-message">Envoyer</button>
    </div>
  </article>
</section>


<script type="module">
  import {
    getAllConversations,
    getAllMessagesForConversation,
    createMessageForConversation,
    getAllParticipantsByConversationId,
    markConversationAsRead
  } from "./services/conversations.js";
  import {
    getConnectedUser
  } from "./services/profile.js";

  const DEFAULT_AVATAR = "assets/default-avatar.png";

  function formatMessageTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString("fr-FR", {
      hour: "2-digit",
      minute: "2-digit"
    });
  }

  function formatMessageDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    return `${day}/${month}`;
  }

  function truncateText(text, maxLength = 40) {
    if (!text) return "";
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength - 1) + "…";
  }


  document.addEventListener("DOMContentLoaded", () => {
    Promise.all([
        getAllConversations(),
        getConnectedUser()
      ])
      .then((values) => {
        const conversations = values[0];
        const connectedUser = values[1];

        if (!connectedUser) {
          window.location.href = "index.php?page=login";
        }

        const convList = document.getElementById('conversation-list');

        //Liste des conversations (colonne de gauche)
        conversations.forEach((conv) => {
          const elem = document.createElement("li");
          elem.setAttribute("id", "conv-" + conv.id);

          //Calcul de l'heure du dernier message (ou de la création de la conv)
          const lastDate = conv.last_message_at || conv.created_at;
          const timeLabel = lastDate ? formatMessageTime(lastDate) : "";

          //Aperçu du dernier message
          const previewRaw = conv.last_message_content || "";
          const preview = truncateText(previewRaw, 40);

          //Structure HTML de base : avatar + nom + heure
          elem.innerHTML = `
          <img class="conv-avatar" src="${DEFAULT_AVATAR}" alt="Avatar" />
          <div class="conv-text">
            <span class="conv-name">Conversation ${conv.id}</span>
            <span class="conv-preview">${preview}</span>
          </div>
          <span class="conv-time">${timeLabel}</span>
        `;

          //On récupère les participants pour afficher le bon nom + avatar
          getAllParticipantsByConversationId(conv.id).then((participants) => {
            // participants est un tableau d'objets { id, username, image }
            const other = participants.find((p) => p.id !== connectedUser.id);

            const avatarImg = elem.querySelector(".conv-avatar");
            const nameSpan = elem.querySelector(".conv-name");

            if (other) {
              nameSpan.textContent = other.username;
              avatarImg.src = other.image || DEFAULT_AVATAR;
              avatarImg.alt = other.username;
            } else {
              nameSpan.textContent = "Conversation " + conv.id;
            }
          });

          //Quand on clique sur la conv on change l'URL avec le bon conversation_id
          elem.addEventListener("click", () => {
            const url = new URL(window.location.href);
            url.searchParams.set("conversation_id", conv.id);
            window.location.href = url.toString();

            markConversationAsRead(conv.id).then();
          });

          convList.appendChild(elem);
        });

        //Conversation sélectionnée (colonne de droite)
        const selectedConv = new URLSearchParams(window.location.search).get('conversation_id');

        if (selectedConv) {
          // Récupération du nom + avatar du participant pour le titre
          getAllParticipantsByConversationId(selectedConv)
            .then((participants) => {
              const other = participants.find((p) => p.id !== connectedUser.id);
              const titleEl = document.getElementById("conversation-title");

              if (other) {
                titleEl.innerHTML = `
                <img class="conversation-avatar" src="${other.image || DEFAULT_AVATAR}" alt="${other.username}" />
                <span>${other.username}</span>
              `;
              } else {
                titleEl.textContent = "Conversation";
              }
            });

          // Marquer la conv active dans la liste
          const elemSelected = document.getElementById("conv-" + selectedConv);
          if (elemSelected) {
            elemSelected.classList.add('active');
          }

          // Charger les messages de la conversation
          getAllMessagesForConversation(selectedConv).then((messages) => {
            const messageList = document.getElementById('messages-list');
            messageList.innerHTML = "";

            messages.forEach(message => {
              const messageElem = document.createElement('li');
              messageElem.innerHTML = `
              <span class="content">${message.content}</span>
              <span class="time">
                ${formatMessageDate(message.created_at)} • ${formatMessageTime(message.created_at)}
              </span>
            `;

              if (message.sender_id === connectedUser.id) {
                messageElem.classList.add('me');
              }

              messageList.appendChild(messageElem);
            });

            // Scroll en bas après chargement des messages
            messageList.scrollTop = messageList.scrollHeight;
          });
        }

        //Envoi d'un message
        document.getElementById("send-message").addEventListener("click", () => {
          const input = document.getElementById('message');
          const messageText = input.value.trim();
          const selectedConv = new URLSearchParams(window.location.search).get('conversation_id');

          if (messageText && selectedConv) {
            createMessageForConversation(selectedConv, messageText).then(() => {
              const messageList = document.getElementById('messages-list');
              const messageElem = document.createElement('li');

              messageElem.innerHTML = `
              <span class="content">${messageText}</span>
              <span class="time">
                ${formatMessageDate(new Date())} • ${formatMessageTime(new Date())}
              </span>
            `;

              messageElem.classList.add('me');

              messageList.appendChild(messageElem);
              input.value = '';

              // scroll automatique
              messageList.scrollTop = messageList.scrollHeight;
            }).catch((err) => {
              console.error(err);
            });
          }
        });
      }).catch(err => {
        window.location.href = "index.php?page=login";
      });
  });
</script>


<?php include './templates/footer.php'; ?>