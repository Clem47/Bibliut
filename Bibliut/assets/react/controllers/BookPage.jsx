import React from "react"
import "semantic-ui-css/semantic.min.css"
import Recherche from "./Recherche"
import Footer from "./Footer"
import Book from "./BookDetails"
import Results from "./Results"

export default function BooksPage() {
  return (
    <div
      style={{ display: "flex", flexDirection: "column", minHeight: "100vh" }}
    >
      <div style={{}}>
        <Recherche />
      </div>

      <div>
        <Book />
      </div>

      <div style={{ marginTop: "auto" }}>
        <Footer />
      </div>
    </div>
  )
}
