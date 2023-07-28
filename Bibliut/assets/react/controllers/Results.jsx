import React, { useEffect, useState } from "react"
import { Card, Image, Button, Pagination } from "semantic-ui-react"
import "semantic-ui-css/semantic.min.css"
import { Link, useLocation } from "react-router-dom"
import axios from "axios"
import BookCard from "./BookCard"

function Results() {
  const [totalPages, setTotalPages] = useState(0)
  const [currentPage, setCurrentPage] = useState(1)
  const [books, setBooks] = useState([])
  const location = useLocation()
  const query = new URLSearchParams(location.search).get("description")
  // recupérer query de l'url

  useEffect(() => {
    axios
      .get("http://185.212.226.162/api/search", {
        params: {
          description: query,
          page: currentPage,
        },
      })
      .then((response) => {
        console.log(response.data)
        setBooks(response.data.books)
        setTotalPages(Math.ceil(response.data.nbPage))
      })
      .catch((error) => {
        console.log(error)
      })
  }, [query, currentPage])

  return (
    <div id="result">
      <BookCard books={books} title="Résultats de la recherche" />
      <div style={{ display : "flex", justifyContent : "center", alignItems : "center", marginBottom:'1em'}}>
      <Pagination
        activePage={currentPage}
        totalPages={totalPages}
        onPageChange={(e, { activePage }) => setCurrentPage(activePage)}
        style={{ marginTop: "1em", alignSelf: "center" }}
      />
      </div>
    </div>
  )
}

export default Results
