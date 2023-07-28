  import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import {Segment, Container, Grid, Header, List, Divider, Image, Icon} from 'semantic-ui-react';
import { useParams } from 'react-router-dom';
import { useState, useEffect } from 'react';
import axios from 'axios';


export default function BookDetails(props) {
    const { bookId } = useParams();
    const [book, setBook] = useState(null);
    console.log(bookId);

    useEffect(() => {
        axios.get(`http://185.212.226.162/api/book/${bookId}`)
            .then((data) => {
                setBook(data);
            })
            .catch((error) => {
                console.log(error);
            });
    }, []);


    
    return (
        <div>
          
            
        <Segment vertical style={{padding: '5em 0em',paddingBottom:'14em', color: 'black'}}>
          {console.log(book)}

        <Header as='h2' content={book?.data.title} style={{color: '#009999'}} textAlign='center' /> 
            <Container style={{marginTop:'3em'}} textAlign='center' >
            <Grid stackable columns={2} style={{marginTop: '1em'}} centered divided>
              <Grid.Column width={6} style={{backgroundColor: '#F5F5F5'}}>
                <Image src={book?.data.image} size='medium' centered/>
              </Grid.Column>
              <Grid.Column width={6} style={{backgroundColor: '#F5F5F5' }}>
                <Grid columns={3}>
                  <Grid.Column>
                    <List relaxed>
                      <List.Item> 
                        {book?.data.language.nameLanguage==='fr' ? <List.Content floated='middle'>Langue : <p style={{color: '#009999'}}>Français</p></List.Content> 
                        : null}
                        
                        {book?.data.language.nameLanguage==='en' ? <List.Content floated='middle'>Langue : <p style={{color: '#009999'}}>Anglais</p></List.Content>
                        : null}

                        {book?.data.language.nameLanguage==='es' ? <List.Content floated='middle'>Langue : <p style={{color: '#009999'}}>Espagnol</p></List.Content>
                        : null}

                        {book?.data.language.nameLanguage==='de' ? <List.Content floated='middle'>Langue : <p style={{color: '#009999'}}>Allemand</p></List.Content>
                        : null}
                      </List.Item>
                      <List.Item>
                        {book?.data.editor.nameEditor === "" ? <List.Content floated='middle'>Editeur : <p style={{color: '#009999'}}>Inconnu</p></List.Content> :
                        <List.Content floated='middle'>Editeur : <p style={{color: '#009999'}}>{book?.data.editor.nameEditor}</p></List.Content>
                        }
                      </List.Item>
                    </List>
                  </Grid.Column>
                  <Grid.Column>
                    <List relaxed>
                      <List.Item>
                        {book?.data.categories[0] === undefined ? <List.Content floated='middle'>Catégorie : <p style={{color: '#009999'}}>Inconnu</p></List.Content> :
                        <List.Content floated='middle'>Catégorie : <p style={{color: '#009999'}}>{book?.data.categories[0].nameCategory}</p></List.Content>}
                      </List.Item>
                      <List.Item>
                        <List.Content floated='middle'>Nombre de pages : <p style={{color: '#009999'}}>{book?.data.nbPages}</p></List.Content> 
                      </List.Item>
                    </List>
                  </Grid.Column>
                  <Grid.Column>
                    <List relaxed>
                      <List.Item>
                        {book?.data.authors[0] === undefined ? <List.Content floated='middle'>Auteur : <p style={{color: '#009999'}}>Inconnu</p></List.Content> :
                        <List.Content floated='middle'>Auteur : <p style={{color: '#009999'}}>{book?.data.authors[0].firstName} {book?.data.authors[0].lastName}</p></List.Content>}
                      </List.Item>
                    </List>
                  </Grid.Column>
                </Grid>
                <Divider />
                
                <Header as='h2' content='Résumé' style={{color: '#009999'}} textAlign='center' /> 
                <p style={{textAlign: 'justify', padding: '0em 1em'}}> {/* Replace content by props.summary */}
                {book?.data.summary}
                </p>
                    
                
              </Grid.Column>
            </Grid>
            </Container>
        </Segment>
        </div>
    
    );
}