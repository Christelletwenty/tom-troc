export const login = (email, password) => {
  const body = new URLSearchParams();
  body.append("email", email);
  body.append("password", password);

  return fetch("api/login.php", {
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

export const getConnectedUser = () => {
  return fetch("api/login.php", {
    method: "GET",
  }).then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok " + response.statusText);
    }
    return response.json();
  });
};
