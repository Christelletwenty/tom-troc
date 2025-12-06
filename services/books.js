export const getAllBooks = () => {
  return fetch("api/books.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getBookById = (id) => {
  return fetch("api/books.php?id=" + id, {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};

export const getBookUserById = (id) => {
  return fetch("api/books.php?user_id=" + id, {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};
