export const getAllBooksByUsername = () => {
  return fetch("api/books.php", {
    method: "GET",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
  }).then((response) => response.json());
};
