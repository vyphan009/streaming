import React, { Fragment, Component } from 'react';
import videojs from 'video.js'
import 'video.js/dist/video-js.css'
import AWS from 'aws-sdk';
import awsmobile from '../aws-exports';
import { Auth } from 'aws-amplify';
import {Promise} from "bluebird";
const config = require('../config.json');

class VideoPlayer extends Component {
  constructor(props) {
    super(props)
    this.state = {
        videos: []
    };
  }

  componentDidMount() {
    this.player = videojs(this.videoNode, this.props)

      const S3 = require('aws-sdk/clients/s3');

      AWS.config.update({
        region: awsmobile.aws_cognito_region,
        accessKeyId: config.bucket.accessKeyId,
        secretAccessKey: config.bucket.secretAccessKey,
      });

      const s3 = new AWS.S3();
      const getS3Data = () => {
        return new Promise((resolve, reject) => {
          const params = {
            Bucket: config.bucket.name,
            Delimiter: '',
            Prefix: '',
          }
          s3.listObjectsV2(params, (err, data) => {
            if (err) reject(err)
            resolve(data.Contents)
          })
        })
      }

    Promise.all([
        getS3Data()
    ]).then(([videos]) => {
        this.setState({videos})
    })
  }

  componentWillUnmount() {
    if (this.player) {
      this.player.dispose()
    }
  }

  renderVideoList() {
    const { videos } = this.state
    return (
        <div>
            <h3>Files:</h3>
            <ul>
                {videos.filter((item) => item.Key.search(/.ts/) == -1).map((item) => <li key={item.ETag}>{item.Key}</li>)}
            </ul>
        </div>
    )
  }

  render() {
    return (
      <>
        <div data-vjs-player style={{
            width: 960, height: 540
          }}>
          <video  ref={(node) => { this.videoNode = node; }} className="video-js" />
        </div>
        { this.renderVideoList() }
      </>
    );
  }
}


const videoJsOptions = {
  autoplay: true,
  controls: true,
  sources: [{
    src: 'http://d33lhde8j1wssd.cloudfront.net/demo.m3u8'
  }]
}

const nav = { padding: '0px 40px', height: 60, borderBottom: '1px solid #ddd', display: 'flex', alignItems: 'center' }
const container = { paddingTop: 40, width: 960, margin: '0 auto' }
const navHeading = { margin: 0, fontSize: 18 }

export default function Home(props){
  const { user } = props.auth
  let groups = user ? props.auth.user.signInUserSession.accessToken.payload["cognito:groups"] : []
  if (!groups){
    groups = []
  }
  return (
    <Fragment>
      <div>
        <p className="has-text-centered">
          <span className="tag is-primary">A treaming app</span>
        </p>
      </div>
      <div>
        { groups.length > 0 && (
          <nav style={nav}>
            <p style={navHeading}>Groups: { groups.map((name) => name)}</p>
          </nav>
        )}
        { groups.includes("PaidUsers") && (
            <div style={container}>
                <VideoPlayer { ...videoJsOptions }/>
            </div>
        )}
      </div>
    </Fragment>
  )
}

