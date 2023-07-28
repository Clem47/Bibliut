import React, { useState, useEffect } from "react"
import Results from "./Results"
import Header from "./Header"
import { Input, Pagination, Form, Button } from "semantic-ui-react"
import "semantic-ui-css/semantic.min.css"
import { useNavigate } from "react-router-dom"

export default function Toto(props) {
  const [author, setTitle] = useState("")
  const [books, setBooks] = useState([])
  const [isOk, setIsOk] = useState(false)
  const [currentPage, setCurrentPage] = useState(1)
  const [totalPages, setTotalPages] = useState(0)
  const navigate = useNavigate()

  useEffect(() => {
    fetch(`http://185.212.226.162/api/search?description=${author}`)
      .then((response) => response.json())
      .then((data) => {
        setBooks(data.books)
        setTotalPages(Math.ceil(data.totalItems / 10))
      })
  }, [author, currentPage])

  // recupérer query

  const handleSubmit = (event) => {
    event.preventDefault()
    navigate(`/search?description=${author}`)
    setIsOk(true)
  }

  return (
    <div
      className="search"
      style={{ display: "flex", flexDirection: "column" }}
    >
      <Header />
      <div class="resize">
        <div class="ui action input">
          <Form size="small" onSubmit={handleSubmit}>
          <Form.Input type="text" placeholder="Rechercher un auteur" id="description" value={author} action = {{color: 'teal', icon: 'search',  onClick: handleSubmit}}
                        onChange={(event) => setTitle(event.target.value)}
                         onKeyPress={(e) => { if (e.key === 'Enter') { handleSubmit() } }}/>
          </Form>
        </div>
      </div>
    </div>
  )
}
