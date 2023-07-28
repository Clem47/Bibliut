import React from "react";
import "semantic-ui-css/semantic.min.css";
import { Container, Segment, Card, Placeholder, Image, Header } from "semantic-ui-react";
import { Link } from "react-router-dom";




export default function BookCard(props) {
    const { books } = props;
    const { title } = props;
    return(
        <Container style={{ paddingTop: "5em", paddingBottom: "5em" }}>
        <Header as="h2" style={{ color: "#009999", paddingBottom: "1em" }}>
            {title}
        </Header>
        <Card.Group stackable itemsPerRow={4}>
            {books.map((item) => (
            <Card>
                <Link to={`/books/${item.id}`}>
                <Image
                    src={item.image}
                    alt={item.title}
                    style={{ height: 200,width: 200 }}
                    centered
                />
                </Link>
                <Card.Content style={{maxHeight: 150,overflow: 'hidden'}}>
                <Card.Header>{item.title}</Card.Header>
                <Card.Meta>{item.release_date}</Card.Meta>
                <Card.Description>{item.authors.first_name}</Card.Description>
                </Card.Content>
            </Card>
            ))}
        </Card.Group>
        </Container>
    )
}