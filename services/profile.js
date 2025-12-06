export const getConnectedUser = () => {
  return fetch("api/profile.php", {
    method: "GET",
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const updateUser = (email, password, username) => {
  const body = new URLSearchParams();
  body.append("email", email);
  body.append("username", username);

  if (password.trim() !== "") {
    body.append("password", password);
  }

  return fetch("api/user.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const updateBook = (id, titre, auteur, image, description, dispo) => {
  const body = new URLSearchParams();
  body.append("action", "update");
  body.append("id", id);
  body.append("titre", titre);
  body.append("auteur", auteur);
  body.append("image", image);
  body.append("description", description);
  body.append("dispo", dispo);

  return fetch("api/books.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const deleteBook = (id) => {
  const body = new URLSearchParams();
  body.append("action", "delete");
  body.append("id", id);

  return fetch("api/books.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const getUserBooks = (userId = null) => {
  const url = userId
    ? `api/books.php?user_id=${encodeURIComponent(userId)}`
    : "api/books.php";

  return fetch(url, {
    method: "GET",
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const createBook = (
  titre,
  auteur,
  description,
  dispo = "1",
  imageFile = null
) => {
  const formData = new FormData();
  formData.append("titre", titre);
  formData.append("auteur", auteur);
  formData.append("description", description);
  formData.append("dispo", dispo);

  if (imageFile) {
    formData.append("image", imageFile);
  }

  return fetch("api/books.php", {
    method: "POST",
    body: formData,
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const getBookById = (id) => {
  return fetch(`api/books.php?id=${encodeURIComponent(id)}`, {
    method: "GET",
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};

export const getUserById = (id) => {
  return fetch(`api/user.php?id=${encodeURIComponent(id)}`, {
    method: "GET",
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};
