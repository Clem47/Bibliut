import React, { useEffect, useState } from "react"
import "semantic-ui-css/semantic.min.css"
import {
  Container,
  Segment,
  Card,
  Placeholder,
  Image,
  Header,
} from "semantic-ui-react"
import { Link } from "react-router-dom"
import axios from "axios"
import BookCard from "./BookCard"

export default function NewBooks(props) {
  const [books, setBooks] = useState([])

  useEffect(() => {
    axios
      .get("http://185.212.226.162/api/books/lastAcquisition?quantity=4")
      .then((response) => {
        console.log(response.data)
        setBooks(response.data.books)
      })
      .catch((error) => {
        console.log(error)
      })
  }, [])

  return (
    <BookCard books={books} title="Les dernières acquisitions de la Bibliothèque" />
  )
}
