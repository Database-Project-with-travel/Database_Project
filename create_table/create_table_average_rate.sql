create table average_rate select extract(year from 日期)-1911 as 年,
	extract(month from 日期) as 月,
	avg(美元／新台幣) as 美元／新台幣,
	avg(人民幣／新台幣) as 人民幣／新台幣,
	avg(歐元／美元) as 歐元／美元,
	avg(美元／日幣) as 美元／日幣,
	avg(英鎊／美元) as 英鎊／美元,
	avg(澳幣／美元) as 澳幣／美元,
	avg(美元／港幣) as 美元／港幣,
	avg(美元／人民幣) as 美元／人民幣,
	avg(美元／南非幣) as 美元／南非幣,
	avg(紐幣／美元) as 紐幣／美元
	from exchange_rate
group by extract(year from 日期)-1911, extract(month from 日期);
