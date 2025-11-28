export const createAccount = (username, email, password) => {
  const body = new URLSearchParams();
  body.append("username", username);
  body.append("email", email);
  body.append("password", password);

  return fetch("api/user.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body,
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};
