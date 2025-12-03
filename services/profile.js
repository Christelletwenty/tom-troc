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

// On garde updateUser pour plus tard, quand ton backend user.php sera prêt
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
