create table rate_to_TWD
select 年, 月, 
       美元／新台幣 as 美元, 
       人民幣／新台幣 as 人民幣, 
       (歐元／美元 * 美元／新台幣) as 歐元, 
       (CASE WHEN (美元／日幣 <> 0) THEN ((1/美元／日幣) * 美元／新台幣) ELSE 0 END) as 日幣, 
       (英鎊／美元 * 美元／新台幣) as 英鎊, 
       (澳幣／美元 * 美元／新台幣) as 澳幣, 
       (CASE WHEN (美元／港幣 <> 0) THEN ((1/美元／港幣) * 美元／新台幣) ELSE 0 END) as 港幣,
       (CASE WHEN (美元／南非幣 <> 0) THEN ((1/美元／南非幣) * 美元／新台幣) ELSE 0 END) as 南非幣,
       (紐幣／美元 * 美元／新台幣) as 紐幣
from average_rate
group by 年, 月;