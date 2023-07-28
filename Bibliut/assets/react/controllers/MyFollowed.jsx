import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import { Container, Header, Card, Image, Button, Segment } from 'semantic-ui-react';
import axios from 'axios';
import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';

const token = localStorage.getItem('token');



export default function MyFollowed() {
    const [users, setUsers] = useState([]);
    const [lastReads, setLastReads] = useState([]);
    
    const searchUsers = async () => {
        try {

            const response = await axios.get('http://185.212.226.162/api/getFollow', {
                method: 'GET',
                headers: {
                'Content-Type' : "application/json",
                'Authorization': `Bearer ${token}`,
            },
            }
            )
            .then(response => {
                const tempUsers = response.data;
                for (let i = 0; i < tempUsers.length; i++) {
                    tempUsers[i]= {...tempUsers[i], lastReads: []}
                    searchLastReads(tempUsers[i]);
                }
                setUsers(tempUsers);
                
            })
        } catch (error) {
            console.log(error);
        }
        }

    const searchLastReads = async user => {
        console.log('je suis rentré dans searchLastReads avec ' + user.username);
        try {
            console.log('lastReadsSearch');
            const response = await axios.get('http://185.212.226.162/api/books', {
                method: 'GET',
                headers: {
                'Content-Type' : "application/json",
            },
                params: {
                    quantity : '3',
                    username : user.username,
                }
                }
                )
                .then(response => {
                    const tempLastReads = response.data.books;
                    setLastReads(tempLastReads);
                    user.lastReads = tempLastReads;
                    

                })
        }
        catch (error) {
            console.log(error);
        }
            
    }

    const handleUnfollow = user => {
        var formData = new FormData();
        formData.append("username", user.username);
        try {
            console.log('unfollow');
            const response = axios.post('http://185.212.226.162/api/unfollow', formData, 
             {
                method: 'POST',
                headers: {
                    'Content-Type' : "multipart/form-data",
                    'Authorization': `Bearer ${token}`,
                },
                }
                )
                .then(response => {
                    console.log('unfollow : ', response.data)
                    window.location.reload();
                })
            }
        catch (error) {
            console.log(error);
        }
    }

    useEffect(() => {
        searchUsers();
    }, []);
  
    return (

        <Container style={{paddingTop:'5em',paddingBottom:'5em'}}>
            {console.log(users)}
        <Header as='h2' style={{color: '#009999', paddingBottom:'1em'}}>Vos abonnements</Header>
        {users.length === 0 && <p>Vous n'avez pas encore d'abonnements</p>}
        <Card.Group stackable itemsPerRow={4}>
            {users.map((item) => (

                <Card key={item.id}>
                    <Card.Content>
                        <Image
                        floated='right'
                        size='mini'
                        src={'http://185.212.226.162/api/profile/'+item.id}
                        />
                        <Card.Header>{item.username}</Card.Header>
                        <Card.Description>
                        Ses dernières lectures
                        </Card.Description>
                    </Card.Content>
                    <Card.Content extra>
                        <Segment textAlign='center' basic>
                            <Image.Group>
                                {item.lastReads.map((book) => (
                                    <Link to={'/books/'+book.id}>
                                    <Image src={book.image} style={{width:'65px', height:'80px'}}/>
                                    </Link>
                                ))}
                            </Image.Group>
                        </Segment>
                    </Card.Content>
                    <Card.Content extra>
                        <div className='ui two buttons'>
                        <Button basic color='red' onClick={() => handleUnfollow(item)}>
                            Se désabonner
                        </Button>
                        </div>
                    </Card.Content>
                </Card>
            ))}
        </Card.Group>
        </Container>   

            );
        }