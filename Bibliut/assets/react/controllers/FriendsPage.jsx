import React from 'react';
import 'semantic-ui-css/semantic.min.css';
import FollowersProposition from './FollowersProposition';
import Footer from './Footer';
import Header from './Header';
import MyFollowed from './MyFollowed';


export default function FriendsPage() {
    return (<div style={{ display : "flex", flexDirection : "column", minHeight : "100vh" }}>
                <div>
                    <Header />
                </div>
                <div>
                    <MyFollowed />
                </div>
                <div>
                    <FollowersProposition />
                </div>
                <div style={{ marginTop : "auto" }}>
                    <Footer />
                </div>
            </div>
            );
}