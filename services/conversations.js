export const getAllConversations = () => {
  return fetch("api/conversations.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getAllMessagesForConversation = (id) => {
  return fetch("api/conversations.php?conversation_id=" + id, {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getAllParticipantsByConversationId = (id) => {
  return fetch(
    "api/conversations.php?conversation_id=" + id + "&participants=true",
    {
      method: "GET",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
    }
  ).then((response) => response.json());
};

export const createConversationWithUserId = (id) => {
  const body = new URLSearchParams();
  body.append("user_id", id);

  return fetch("api/conversations.php", {
    method: "POST",
    body,
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const createMessageForConversation = (conversationId, message) => {
  const body = new URLSearchParams();
  body.append("conversation_id", conversationId);
  body.append("content", message);

  return fetch("api/conversations.php", {
    method: "POST",
    body,
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};
