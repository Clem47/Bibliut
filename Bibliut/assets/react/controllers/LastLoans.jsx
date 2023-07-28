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

const token = localStorage.getItem('token');
const username = localStorage.getItem('username');

export default function LastLoans(props) {
  const [books, setBooks] = useState([])

  
  useEffect( () => {
    axios
      .get("http://185.212.226.162/api/books",{
        method: 'GET',
        headers: {
        'Content-Type' : "application/json",
        'Authorization': `Bearer ${token}`,
      },
      params: {
        quantity : '4',
        username : username,
    }
      })

      .then((response) => {
        console.log(response.data)
        setBooks(response.data.books)
      })
      .catch((error) => {
        console.log(error)
      })
  }, [])

  return (
    <BookCard books={books} title="Vos derniers livres empruntés" />
  )
}
