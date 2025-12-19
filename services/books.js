export const getAllBooks = () => {
  return fetch("api/index.php?page=books", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getBookById = (id) => {
  return fetch("api/index.php?page=books&id=" + id, {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getBookUserById = (id) => {
  return fetch("api/index.php?page=books&user_id=" + id, {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};
