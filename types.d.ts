export type Testimonial = {
  name: string,
  country: string,
  testimonial: string,
  img_url: string,
}

export type NewsArticle = {
  title: string,
  author: string,
  url: string,
  urlToImage: string,
  publishedAt: Date,
  description: string,
}

export type ConferenceRegistrant = {
  full_name: string,
  email: string,
  phone: string,
  amount: string,
  password?: string,
  conference_tag: string,
  is_manual_reg: boolean,
}
