<?php
/*
Plugin Name: All-in-one Bookmarking Button
Version: 1.1
Plugin URI: http://www.socialmarker.com
Description: Adds the <a href="http://www.socialmarker.com/">SocialMarker.com</a> bookmarking button at the bottom of every post.
Author: King Kong
*/

/* Copyright 2007 Sorin Iclanzan (email : contact@socialmarker.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

*/

function sm_utf8_to_unicode( $str ) {
	$unicode    = array();
	$values     = array();
	$lookingFor = 1;
	for ( $i = 0; $i < strlen( $str ); $i ++ ) {
		$thisValue = ord( $str[ $i ] );
		if ( $thisValue < 128 ) {
			$unicode[] = $thisValue;
		} else {
			if ( count( $values ) == 0 ) {
				$lookingFor = ( $thisValue < 224 ) ? 2 : 3;
			}
			$values[] = $thisValue;
			if ( count( $values ) == $lookingFor ) {
				$number     = ( $lookingFor == 3 ) ?
					( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ) :
					( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
				$unicode[]  = $number;
				$values     = array();
				$lookingFor = 1;
			}
		}
	}

	return $unicode;
}

function sm_unicode_to_ascii( $unicode ) {
	$entities = '';
	$unicode  = sm_utf8_to_unicode( $unicode );
	foreach ( $unicode as $value ) {
		$entities .= ( $value > 127 ) ? '&#' . $value . ';' : chr( $value );
	}

	return $entities;
}

function sm_gen_keywords( $content ) {
	require_once( 'class.autokeyword.php' );
	$params['content'] = $content; //page content
	//set the length of keywords you like
	$params['min_word_length']          = 5;  //minimum length of single words
	$params['min_word_occur']           = 5;  //minimum occur of single words
	$params['min_2words_length']        = 3;  //minimum length of words for 2 word phrases
	$params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
	$params['min_2words_phrase_occur']  = 3; //minimum occur of 2 words phrase
	$keyword                            = new sm_autokeyword( $params );

	return $keyword->parse_words();
}

function sm_gen_description( $content, $length ) {
	$description = substr( strip_tags( str_replace( array( "\r\n", "\n", "\r", "&nbsp; &bull; &nbsp;" ), " ", $content ) ), 0, $length );
	$poz1        = strrpos( $description, "." );
	if ( $poz1 == false || substr( $description, $poz1 + 1, 1 ) != " " ) {
		$poz1 = 0;
	}
	$poz2 = strrpos( $description, "?" );
	if ( $poz2 == false || substr( $description, $poz2 + 1, 1 ) != " " ) {
		$poz2 = 0;
	}
	$poz3 = strrpos( $description, "!" );
	if ( $poz3 == false || substr( $description, $poz3 + 1, 1 ) != " " ) {
		$poz3 = 0;
	}
	$poz = max( $poz1, $poz2, $poz3 );
	if ( $poz == 0 || $poz < $length - 100 ) {
		$poz         = strrpos( $description, " " );
		$description = substr( $description, 0, $poz ) . "...";
	} else {
		$description = substr( $description, 0, $poz + 1 );
	}

	return $description;
}

function encodeText($text) {
	return urlencode( sm_gen_description( sm_unicode_to_ascii( $text ), 200 ) );
}

function insert_socialmarker_button( $content ) {

	if ( ! is_page() ) {
		$content .= '<div style="display: flex; margin-top: 60px;">';
		$content .= facebookLink($content);
		$content .= twitterLink($content);
		$content .= pinterestLink($content);
		$content .= linkedinLink($content);
		$content .= socialMarkerLink($content);
		$content .= '</div>';
	}

	return $content;
}
add_action( 'the_content', 'insert_socialmarker_button' );


function facebookLink($content) {

	$link =     '<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=' . get_permalink() . '">';
	$link .=        '<img style="width: 32px; height: 32px; margin-right: 10px;" src="' . plugins_url('/icons/facebook.png', __FILE__) . '" />';
	$link .=    '</a>';

	return $link;
}

function twitterLink($content) {

	$link =     '<a target="_blank" href="https://twitter.com/home?status=' . get_permalink() . ' ' . encodeText($content) . '">';
	$link .=        '<img style="width: 32px; height: 32px; margin-right: 10px;" src="' . plugins_url('/icons/twitter.png', __FILE__) . '" />';
	$link .=    '</a>';

	return $link;
}

function pinterestLink($content) {

	$link =     '<a target="_blank" href="https://pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=&description=' . encodeText($content) . '">';
	$link .=        '<img style="width: 32px; height: 32px; margin-right: 10px;" src="' . plugins_url('/icons/pinterest.png', __FILE__) . '" />';
	$link .=    '</a>';

	return $link;
}

function linkedinLink($content) {

	$link =     '<a target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url=' . get_permalink() . '&title=&summary=' . encodeText($content) . '">';
	$link .=        '<img style="width: 32px; height: 32px; margin-right: 10px;" src="' . plugins_url('/icons/linkedin.png', __FILE__) . '" />';
	$link .=    '</a>';

	return $link;
}

function socialMarkerLink($content) {
	$link =     '<a target="_blank" href="http://www.socialmarker.com/?link=' . get_permalink() . '&title=' . urlencode( sm_unicode_to_ascii( the_title( '', '', false ) ) ) . '&text=' . encodeText($content) . '">';
	$link .=        '<img style="width: 32px; height: 32px; margin-right: 10px; border-radius: 4px;" src="' . plugins_url('/icons/socialmarker.png', __FILE__) . '" />';
	$link .=    '</a>';

	return $link;
}


?>

